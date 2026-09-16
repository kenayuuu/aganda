<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BonusAllocation;
use App\Models\BonusWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminWithdrawalController extends Controller
{
    private function authorizeAdmin(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(response()->json([
                'message' => 'Hanya admin yang dapat mengakses data pencairan.',
            ], 403));
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        $withdrawals = BonusWithdrawal::query()
            ->with([
                'user',
                'processedBy',
            ])
            ->latest('id')
            ->get();

        return response()->json([
            'withdrawals' => $withdrawals->map(function ($withdrawal) {
                return [
                    'id' => $withdrawal->id,
                    'amount' => (float) $withdrawal->amount,
                    'status' => $withdrawal->status,
                    'requested_at' => $withdrawal->requested_at,
                    'processed_at' => $withdrawal->processed_at,
                    'notes' => $withdrawal->notes,
                    'user' => $withdrawal->user ? [
                        'id' => $withdrawal->user->id,
                        'name' => $withdrawal->user->name,
                        'email' => $withdrawal->user->email,
                        'member_id' => $withdrawal->user->member_id,
                        'role' => $withdrawal->user->role,
                    ] : null,
                    'processed_by' => $withdrawal->processedBy ? [
                        'id' => $withdrawal->processedBy->id,
                        'name' => $withdrawal->processedBy->name,
                    ] : null,
                ];
            })->values(),
        ]);
    }

    public function approve(Request $request, BonusWithdrawal $withdrawal)
    {
        $this->authorizeAdmin($request);

        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'message' => 'Withdrawal hanya dapat disetujui jika statusnya pending.',
                'status' => $withdrawal->status,
            ], 422);
        }

        $withdrawal->update([
            'status' => 'approved',
            'processed_at' => now(),
            'processed_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Pengajuan pencairan berhasil disetujui.',
            'withdrawal' => [
                'id' => $withdrawal->id,
                'amount' => (float) $withdrawal->amount,
                'status' => $withdrawal->status,
                'processed_at' => $withdrawal->processed_at,
                'processed_by' => $request->user()->id,
            ],
        ]);
    }

    public function reject(Request $request, BonusWithdrawal $withdrawal)
    {
        $this->authorizeAdmin($request);

        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'message' => 'Withdrawal hanya dapat ditolak jika statusnya pending.',
                'status' => $withdrawal->status,
            ], 422);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $withdrawal, $validated) {
            BonusAllocation::where('user_id', $withdrawal->user_id)
                ->where('allocation_type', 'withdrawal')
                ->where('reference_id', $withdrawal->id)
                ->delete();

            $withdrawal->update([
                'status' => 'rejected',
                'processed_at' => now(),
                'processed_by' => $request->user()->id,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return response()->json([
            'message' => 'Pengajuan pencairan ditolak dan bonus dikembalikan ke saldo tersedia.',
            'withdrawal' => [
                'id' => $withdrawal->id,
                'amount' => (float) $withdrawal->amount,
                'status' => $withdrawal->status,
                'processed_at' => $withdrawal->processed_at,
                'processed_by' => $request->user()->id,
                'notes' => $withdrawal->notes,
            ],
        ]);
    }

    public function paid(Request $request, BonusWithdrawal $withdrawal)
    {
        $this->authorizeAdmin($request);

        if ($withdrawal->status !== 'approved') {
            return response()->json([
                'message' => 'Withdrawal harus berstatus approved sebelum ditandai sebagai paid.',
                'status' => $withdrawal->status,
            ], 422);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $withdrawal->update([
            'status' => 'paid',
            'processed_at' => now(),
            'processed_by' => $request->user()->id,
            'notes' => $validated['notes'] ?? $withdrawal->notes,
        ]);

        return response()->json([
            'message' => 'Pencairan berhasil ditandai sebagai paid.',
            'withdrawal' => [
                'id' => $withdrawal->id,
                'amount' => (float) $withdrawal->amount,
                'status' => $withdrawal->status,
                'processed_at' => $withdrawal->processed_at,
                'processed_by' => $request->user()->id,
                'notes' => $withdrawal->notes,
            ],
        ]);
    }
}
