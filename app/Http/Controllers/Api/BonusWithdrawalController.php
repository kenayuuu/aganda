<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BonusWithdrawal;
use App\Services\BonusService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BonusWithdrawalController extends Controller
{
    public function index(Request $request)
{
    $user = $request->user();

    if (!in_array($user->role, ['karyawan', 'member'])) {
        return response()->json([
            'message' => 'Anda tidak memiliki akses ke data pencairan bonus.',
        ], 403);
    }

    $bonusService = app(BonusService::class);

    $totalBonus = $bonusService->getTotalBonus($user);
    $totalAllocated = $bonusService->getTotalAllocated($user);
    $availableBonus = $bonusService->getAvailableBonus($user);
    $pendingWithdrawal = $bonusService->getPendingWithdrawal($user);
    $availableForWithdrawal =
        $bonusService->getAvailableBonusForWithdrawal($user);

    $packagePrice = $bonusService->getPackagePrice($user);
    $totalPaid = $bonusService->getTotalPaid($user);
    $remainingPackage = $bonusService->getRemainingPackage($user);
    $isPackagePaidOff = $bonusService->isPackagePaidOff($user);

    if ($user->role === 'karyawan') {
        $canWithdraw = $availableForWithdrawal > 0;
    } else {
        $canWithdraw =
            $isPackagePaidOff
            && $availableForWithdrawal > 0;
    }

    $withdrawals = BonusWithdrawal::where('user_id', $user->id)
        ->latest('id')
        ->get();

    return response()->json([
        'bonus' => [
            'total' => $totalBonus,
            'allocated' => $totalAllocated,
            'available' => $availableBonus,
            'pending_withdrawal' => $pendingWithdrawal,
            'available_for_withdrawal' => $availableForWithdrawal,
        ],

        'package' => [
            'price' => $packagePrice,
            'total_paid' => $totalPaid,
            'remaining' => $remainingPackage,
            'is_paid_off' => $isPackagePaidOff,
        ],

        'can_withdraw' => $canWithdraw,

        'withdrawals' => $withdrawals->map(function ($withdrawal) {
            return [
                'id' => $withdrawal->id,
                'amount' => (float) $withdrawal->amount,
                'status' => $withdrawal->status,
                'requested_at' => $withdrawal->requested_at,
                'processed_at' => $withdrawal->processed_at,
                'notes' => $withdrawal->notes,
                'created_at' => $withdrawal->created_at,
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

        $bonusService = app(BonusService::class);

        try {
            $withdrawal = $bonusService->createWithdrawal(
                $user,
                (float) $validated['amount']
            );
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'amount' => [$e->getMessage()],
            ]);
        }

        return response()->json([
            'message' => 'Pengajuan pencairan bonus berhasil dikirim dan menunggu konfirmasi admin.',
            'withdrawal' => [
                'id' => $withdrawal->id,
                'amount' => (float) $withdrawal->amount,
                'status' => $withdrawal->status,
                'requested_at' => $withdrawal->requested_at,
            ],
        ], 201);
    }
}
