<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\UserReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = UserReport::query()->with('user');

        // Filter by status (open, solved)
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->query('date'));
        }

        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min(100, $perPage));

        $reports = $query->latest('id')->paginate($perPage);

        return ApiResponse::pagination($reports, 'User reports retrieved.', 'reports');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'issue' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validation($validator->errors()->toArray());
        }

        $user = $request->user();
        if (!$user) {
            return ApiResponse::unauthorized();
        }

        $report = UserReport::create([
            'user_id' => $user->id,
            'issue' => $request->input('issue'),
            'category' => $request->input('category'),
            'status' => 'open',
        ]);

        return ApiResponse::created([
            'report' => $report->load('user'),
        ], 'Report submitted successfully.');
    }
}
