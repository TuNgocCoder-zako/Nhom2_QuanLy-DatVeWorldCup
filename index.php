<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

[$container, $router] = require __DIR__.'/bootstrap/app.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath !== '' && $basePath !== '/' && str_starts_with($requestUri, $basePath)) {
    $requestUri = substr($requestUri, strlen($basePath)) ?: '/';
}
// XAMPP commonly serves this project below /PROJECT/Nhom2_QuanLy-DatVeWorldCup.
// API routes are absolute inside the router, so also normalize a nested /api path.
$apiOffset = strpos($requestUri, '/api/');
if ($apiOffset !== false) $requestUri = substr($requestUri, $apiOffset);
$rawBody = file_get_contents('php://input');
$input = $_POST;
$contentType = strtolower($_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '');
$decoded = null;
if (!empty($rawBody)) {
    $maybeJson = json_decode($rawBody, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($maybeJson)) {
        $decoded = $maybeJson;
        $input = array_merge($input, $decoded);
    }
}

$server = $_SERVER;
if (is_array($decoded) && empty($server['CONTENT_TYPE'])) {
    $server['CONTENT_TYPE'] = 'application/json';
}

$request = Request::create($requestUri, $_SERVER['REQUEST_METHOD'] ?? 'GET', $input, $_COOKIE, $_FILES, $server, $rawBody);
if (is_array($decoded)) {
    $request->request->replace($input);
}
$container->instance('request', $request);
$container->instance(Request::class, $request);

$request->setUserResolver(static function (): ?User {
    $userId = $_SESSION['user_id'] ?? null;
    return $userId ? User::query()->find((int) $userId) : null;
});

try {
    $response = $router->dispatch($request);
} catch (HttpExceptionInterface $exception) {
    $response = new JsonResponse([
        'success' => false,
        'message' => $exception->getMessage() ?: 'Không tìm thấy endpoint.',
        'data' => null,
    ], $exception->getStatusCode());
} catch (Throwable $exception) {
    $logDir = __DIR__.'/storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
    }
    @file_put_contents($logDir.'/laravel.log', '['.date('Y-m-d H:i:s').'] '.$exception->getMessage().PHP_EOL.$exception->getTraceAsString().PHP_EOL, FILE_APPEND);
    error_log((string) $exception);

    $debug = filter_var($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?? false, FILTER_VALIDATE_BOOLEAN);
    $responseData = [
        'success' => false,
        'message' => 'Lỗi máy chủ nội bộ.',
        'data' => null,
    ];
    if ($debug) {
        $responseData['debug'] = [
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
    }
    $response = new JsonResponse($responseData, 500);
}
$response->send();
