<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Facade;
use Illuminate\Config\Repository;
use Psr\Log\NullLogger;

require dirname(__DIR__).'/vendor/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('worldcup2030_session');
    $sessionPath = dirname(__DIR__).'/storage/sessions';
    if (!is_dir($sessionPath)) mkdir($sessionPath, 0775, true);
    session_save_path($sessionPath);
    session_start();
}

$envPath = dirname(__DIR__).'/.env';
$env = [];
if (is_file($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $val = trim(trim($value), "\"'");
        $env[$key] = $val;
        $_ENV[$key] = $val;
        putenv("$key=$val");
    }
}
$container = new Container();
Container::setInstance($container);
Facade::setFacadeApplication($container);

$mysqlConfig = [
    'driver' => 'mysql',
    'host' => $env['DB_HOST'] ?? '127.0.0.1',
    'port' => (int) ($env['DB_PORT'] ?? 3306),
    'database' => $env['DB_DATABASE'] ?? 'worldcup_2030',
    'username' => $env['DB_USERNAME'] ?? 'root',
    'password' => $env['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
];
$socketPath = $env['DB_SOCKET'] ?? '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';
if (!empty($socketPath) && file_exists($socketPath)) {
    $mysqlConfig['unix_socket'] = $socketPath;
}

$config = new Repository(['database' => [
    'default' => $env['DB_CONNECTION'] ?? 'mysql',
    'connections' => ['mysql' => $mysqlConfig],
]]);
$container->instance('config', $config);
$container->instance('log', new NullLogger());

$translator = new \Illuminate\Translation\Translator(new \Illuminate\Translation\ArrayLoader(), 'vi');
$container->instance('translator', $translator);

$capsule = new Capsule($container);
$capsule->addConnection($config->get('database.connections.mysql'));
$capsule->setEventDispatcher(new Dispatcher($container));
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container->instance('db', $capsule->getDatabaseManager());

$router = new Router(new Dispatcher($container), $container);
$container->instance('router', $router);
require dirname(__DIR__).'/routes/web.php';

return [$container, $router];
