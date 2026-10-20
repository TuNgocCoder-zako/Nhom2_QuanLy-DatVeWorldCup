<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceController extends ApiController
{
    public function __construct(private BookingService $bookingService) {}

    /** POST /api/maintenance/release-expired-holds */
    public function releaseExpiredHolds(Request $request): JsonResponse
    {
        $admin = $this->requireAdmin($request);
        if ($admin instanceof JsonResponse) return $admin;
        return $this->serviceResponse($this->bookingService->releaseExpiredHolds());
    }
}
