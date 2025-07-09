<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Reimbursement;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $categories = Category::all()->map(function ($category) use ($user) {
                $data = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'limit_per_month' => $category->limit_per_month,
                ];

                if ($user->role === 'employee') {
                    $used = Reimbursement::where('user_id', $user->id)
                        ->where('category_id', $category->id)
                        ->whereMonth('submitted_at', now()->month)
                        ->whereYear('submitted_at', now()->year)
                        ->whereIn('status', ['pending', 'approved'])
                        ->sum('amount');

                    $data['sisa_limit'] = $category->limit_per_month - $used;
                }

                return $data;
            });

            return response()->json([
                'success' => true,
                'message' => 'Categories fetched successfully.',
                'data' => $categories,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching categories.',
            ], 500);
        }
    }
}
