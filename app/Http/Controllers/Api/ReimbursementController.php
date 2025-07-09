<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\EmailLog;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewReimbursementMail;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Validator;

class ReimbursementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Reimbursement::with(['user', 'category']);

            if ($request->user()->role === 'admin') {
                if ($request->trashed === 'only') {
                    $query->onlyTrashed();
                } elseif ($request->trashed === 'with') {
                    $query->withoutTrashed();
                } else {
                    $query->withTrashed();
                }
            } elseif ($request->user()->role === 'employee') {
                $query->where('user_id', $request->user()->id);
            } 

            if ($request->filled('month')) {
                $query->whereMonth('submitted_at', $request->month);
            }

            if ($request->filled('year')) {
                $query->whereYear('submitted_at', $request->year);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->toJson();

        } catch (Exception $e) {
            \Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching reimbursements.'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();

            $category = Category::findOrFail($validated['category_id']);

            $monthTotal = Reimbursement::where('user_id', $request->user()->id)
                ->where('category_id', $category->id)
                ->whereMonth('submitted_at', now()->month)
                ->whereYear('submitted_at', now()->year)
                ->whereIn('status', ['pending', 'approved'])
                ->sum('amount');

            $sisaLimit = $category->limit_per_month - $monthTotal;

            if (($monthTotal + $validated['amount']) > $category->limit_per_month) {
                return response()->json([
                    'success' => false,
                    'message' => "Sisa limit kategori {$category->name} bulan ini hanya Rp " 
                        . number_format($sisaLimit, 0, ',', '.') . ".",
                ], 422);
            }

            if ($request->hasFile('receipt')) {
                $path = $request->file('receipt')->store('receipts', 'public');
            }

            $reimbursement = Reimbursement::create([
                'user_id' => $request->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'amount' => $validated['amount'],
                'category_id' => $validated['category_id'],
                'submitted_at' => now(),
                'receipt_path' => $path ?? null,
            ]);

            activity()
                ->performedOn($reimbursement)
                ->causedBy($request->user())
                ->withProperties([
                    'title' => $reimbursement->title,
                    'amount' => $reimbursement->amount,
                    'category' => $reimbursement->category->name,
                ])
                ->log('Pengajuan reimbursement baru');

            try {
                $managers = User::where('role', 'manager')->get();
                $emails = $managers->pluck('email')->toArray();

                Mail::to($emails)->queue(new NewReimbursementMail($reimbursement));

                EmailLog::create([
                    'to' => implode(', ', $emails),
                    'subject' => "Pengajuan Reimbursement Baru: {$reimbursement->title}",
                    'body' => "Telah diajukan reimbursement sebesar Rp " . number_format($reimbursement->amount, 0, ',', '.') . " oleh {$request->user()->name}",
                    'status' => 'queued',
                ]);
            } catch (Exception $e) {
                Log::error("Email gagal dikirim: " . $e->getMessage());
                EmailLog::create([
                    'to' => implode(', ', $emails ?? []),
                    'subject' => "Pengajuan Reimbursement Baru: {$reimbursement->title}",
                    'body' => "Telah diajukan reimbursement sebesar Rp " . number_format($reimbursement->amount, 0, ',', '.') . " oleh {$request->user()->name}",
                    'status' => 'failed',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reimbursement berhasil diajukan dan email telah dikirim ke manager.',
                'data' => $reimbursement->load(['user', 'category']),
            ], 200);

        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while creating reimbursement.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $reimbursement = Reimbursement::withTrashed()
            ->with(['user', 'category'])
            ->find($id);

        if (!$reimbursement) {
            return response()->json([
                'success' => false,
                'message' => 'Reimbursement not found.'
            ], 404);
        }

        // Ambil path file jika ada
        $fileUrl = null;
        if ($reimbursement->receipt_path) {
            $fileUrl = url('storage/' . $reimbursement->receipt_path);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reimbursement retrieved successfully.',
            'data' => [
                'id' => $reimbursement->id,
                'title' => $reimbursement->title,
                'description' => $reimbursement->description,
                'amount' => $reimbursement->amount,
                'status' => $reimbursement->status,
                'submitted_at' => $reimbursement->submitted_at,
                'approved_at' => $reimbursement->approved_at,
                'deleted_at' => $reimbursement->deleted_at,
                'category' => $reimbursement->category,
                'user' => $reimbursement->user,
                'file_path' => $reimbursement->receipt_path,
                'file_url' => $fileUrl,
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reimbursement $reimbursement)
    {
        if (!$reimbursement) {
            return response()->json([
                'success' => false,
                'message' => 'Reimbursement not found.'
            ], 404);
        }

        if ($request->user()->role !== 'manager') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only manager can approve or reject reimbursement.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $reimbursement->update([
            'status' => $validated['status'],
            'approved_at' => now(),
        ]);

        activity()
            ->performedOn($reimbursement)
            ->causedBy($request->user())
            ->withProperties([
                'status' => $validated['status']
            ])
            ->log('Mengubah status reimbursement menjadi ' . $validated['status']);

        return response()->json([
            'success' => true,
            'message' => 'Reimbursement updated successfully.',
            'data' => $reimbursement->load(['user', 'category'])
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reimbursement $reimbursement)
    {
        if (!$reimbursement) {
            return response()->json([
                'success' => false,
                'message' => 'Reimbursement not found.'
            ], 404);
        }

        $reimbursement->delete();

        activity()
            ->performedOn($reimbursement)
            ->causedBy(auth()->user())
            ->withProperties([
                'title' => $reimbursement->title
            ])
            ->log('Menghapus reimbursement');

        return response()->json([
            'success' => true,
            'message' => 'Reimbursement deleted successfully.'
        ], 200);
    }
}
