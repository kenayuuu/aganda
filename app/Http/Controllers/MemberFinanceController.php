<?php

namespace App\Http\Controllers;

use App\Models\BonusTransaction;
use App\Models\CalonPayment;
use App\Services\BonusService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberFinanceController extends Controller
{
    public function bonus(Request $request, BonusService $bonusService): View
    {
        $member = $request->user();

        $totalBonus = $bonusService->getTotalBonus($member);
        $availableBonus = $bonusService->getAvailableBonus($member);
        $usedForPackage = $bonusService->getPackagePayment($member);
        $cashWithdrawals = $bonusService->getTotalCashWithdrawal($member);

        $line1Transactions = BonusTransaction::query()
            ->where('user_id', $member->id)
            ->where('type', 'line_1')
            ->where('status', 'confirmed');

        $line2Transactions = BonusTransaction::query()
            ->where('user_id', $member->id)
            ->where('type', 'line_2_pairing')
            ->where('status', 'confirmed');

        $line1Count = (clone $line1Transactions)->count();
        $line1Amount = (float) (clone $line1Transactions)->sum('amount');
        $line2Count = (clone $line2Transactions)->count();
        $line2Amount = (float) (clone $line2Transactions)->sum('amount');

        $transactions = BonusTransaction::query()
            ->with('sourceUser')
            ->where('user_id', $member->id)
            ->latest('created_at')
            ->paginate(15);

        return view('member.finance.bonus', compact(
            'member',
            'totalBonus',
            'availableBonus',
            'usedForPackage',
            'cashWithdrawals',
            'line1Count',
            'line1Amount',
            'line2Count',
            'line2Amount',
            'transactions'
        ));
    }

    public function payments(Request $request): View
    {
        $member = $request->user();

        $paymentsQuery = CalonPayment::query()
            ->with(['packageKegiatan', 'confirmedBy']);

        if ($member->calon_id) {
            $paymentsQuery->where('calon_id', $member->calon_id);
        } else {
            $paymentsQuery->whereRaw('1 = 0');
        }

        $payments = $paymentsQuery
            ->latest('created_at')
            ->paginate(15);

        return view('member.finance.payments', compact('member', 'payments'));
    }
}
