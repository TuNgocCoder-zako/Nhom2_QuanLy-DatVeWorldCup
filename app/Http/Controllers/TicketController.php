<?php

namespace App\Http\Controllers;

use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends ApiController
{
    public function __construct(private TicketService $ticketService) {}

    /** GET /api/users/{userId}/tickets */
    public function indexForUser(Request $request, int $userId): JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof JsonResponse) return $user;
        if (!$user->isAdmin() && (int) $user->id !== $userId) {
            return $this->response(false, 'Bạn không có quyền xem vé của người dùng khác.', null, 403);
        }
        $perPage = (int) $request->query('per_page', 15);
        $result = $this->ticketService->getUserTickets($userId, ['per_page' => $perPage]);
        return $this->serviceResponse($result);
    }

    /** GET /api/tickets/{code} */
    public function show(Request $request, string $code): JsonResponse
    {
        $result = $this->ticketService->getTicketDetailByCode($code);
        return $this->serviceResponse($result);
    }
}
