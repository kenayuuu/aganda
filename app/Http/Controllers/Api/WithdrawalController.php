<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BonusWithdrawal;
use App\Services\BonusService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class WithdrawalController extends Controller
{
    public function __construct(
        private BonusService $bonusService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['karyawan', 'member'])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke pencairan bonus.',
            ], 403);
        }

        $withdrawals = BonusWithdrawal::query()
            ->where('user_id', $user->id)
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
                ];
            })->values(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['karyawan', 'member'])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengajukan pencairan bonus.',
            ], 403);
        }

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],
        ]);

        try {
            $withdrawal = $this->bonusService->createWithdrawal(
                $user,
                (float) $validated['amount']
            );
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        $availableBonus = $this->bonusService->getAvailableBonus($user);

        return response()->json([
            'message' => 'Pengajuan pencairan berhasil dibuat.',
            'withdrawal' => [
                'id' => $withdrawal->id,
                'amount' => (float) $withdrawal->amount,
                'status' => $withdrawal->status,
                'requested_at' => $withdrawal->requested_at,
            ],
            'available_bonus' => $availableBonus,
        ], 201);
    }
}
