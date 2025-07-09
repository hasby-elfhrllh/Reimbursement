<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reimbursement;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $query = Reimbursement::query();
            $query->whereMonth('submitted_at', now()->month)
                  ->whereYear('submitted_at', now()->year);
            if ($user->role === 'employee') {
                $query->where('user_id', $user->id);
            } elseif ($user->role === 'manager') {
                $query->whereNull('deleted_at');
            } elseif ($user->role === 'admin') {
                $query = Reimbursement::withTrashed()
                    ->whereMonth('submitted_at', now()->month)
                    ->whereYear('submitted_at', now()->year);
            }
            $data = $query
                ->selectRaw('status, COUNT(*) as count, SUM(amount) as total_amount')
                ->groupBy('status')
                ->get();

            $statuses = ['pending', 'approved', 'rejected'];
            $result = [];

            foreach ($statuses as $status) {
                $item = $data->firstWhere('status', $status);

                $result[$status] = [
                    'count' => $item ? (int)$item->count : 0,
                    'total_amount' => $item ? (float)$item->total_amount : 0.0,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data fetched successfully.',
                'data' => $result
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error fetching dashboard data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching dashboard data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}