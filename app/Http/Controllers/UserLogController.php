<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\UserLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = UserLog::query()->with('user');

        // Filter by status (success / failed)
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Filter by activity
        if ($request->filled('activity')) {
            $query->where('activity', 'like', '%' . $request->query('activity') . '%');
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->query('date'));
        }

        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min(100, $perPage));

        $logs = $query->latest('id')->paginate($perPage);

        return ApiResponse::pagination($logs, 'User logs retrieved.', 'logs');
    }
}
