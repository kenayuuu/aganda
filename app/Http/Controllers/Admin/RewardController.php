<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgandaGroup;
use App\Models\AgandaRewardAward;
use App\Models\User;
use App\Services\RewardProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RewardController extends Controller
{
    public function index(Request $request, RewardProgressService $progressService): View
    {
        $rewardData = $progressService->dashboard();
        $members = $rewardData['members'];

        if ($request->filled('search')) {
            $search = Str::lower((string) $request->string('search'));
            $members = $members->filter(fn (array $row): bool => str_contains(Str::lower($row['member']->name), $search)
                || str_contains(Str::lower((string) $row['member']->member_id), $search));
        }

        if ($request->filled('level')) {
            $selectedLevel = (int) $request->input('level');
            $members = $members->filter(fn (array $row): bool => $row['levels']->contains('level', $selectedLevel));
        }

        if ($request->input('status') === 'achieved') {
            $members = $members->filter(fn (array $row): bool => $row['levels']->contains(fn (array $level): bool => $level['eligible']));
        } elseif ($request->input('status') === 'pending') {
            $members = $members->filter(fn (array $row): bool => $row['levels']->contains(fn (array $level): bool => $level['status'] === 'Menunggu Verifikasi'));
        } elseif ($request->input('status') === 'in_progress') {
            $members = $members->filter(fn (array $row): bool => in_array($row['status'], ['Sedang Berjalan', 'Hampir Tercapai'], true));
        } elseif ($request->input('status') === 'not_started') {
            $members = $members->filter(fn (array $row): bool => $row['status'] === 'Belum Mulai');
        }

        if ($request->input('sort') === 'progress') {
            $members = $members->sortByDesc('progress_percent');
        } else {
            $members = $members->sortBy(fn (array $row): string => Str::lower($row['member']->name));
        }

        $members = $members->values();
        $perPage = 15;
        $page = max(1, $request->integer('page', 1));
        $paginatedMembers = new \Illuminate\Pagination\LengthAwarePaginator(
            $members->forPage($page, $perPage)->values(),
            $members->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $topMembers = $rewardData['members']
            ->filter(fn (array $row): bool => $row['successful_jamaah_count'] > 0)
            ->sortByDesc('progress_percent')
            ->take(5)
            ->values();

        return view('admin.rewards.index', [
            'summary' => $rewardData['summary'],
            'levelSummaries' => $rewardData['levels'],
            'members' => $paginatedMembers,
            'topMembers' => $topMembers,
            'rewardLevels' => $progressService->levels(),
        ]);
    }

    public function show(AgandaGroup $group, User $user, RewardProgressService $progressService): View
    {
        $detail = $progressService->memberDetail((int) $group->id, (int) $user->id);

        abort_if($detail === null, 404);

        return view('admin.rewards.show', [
            'detail' => $detail,
            'group' => $group,
        ]);
    }

    public function verify(
        Request $request,
        AgandaGroup $group,
        User $user,
        int $level,
        RewardProgressService $progressService
    ) {
        $levelDefinition = collect($progressService->levels())->firstWhere('level', $level);

        abort_if($levelDefinition === null, 404);

        $detail = $progressService->memberDetail((int) $group->id, (int) $user->id);

        abort_if($detail === null, 404);

        $levelProgress = $detail['levels']->firstWhere('level', $level);

        if (! $levelProgress['eligible']) {
            return back()->with('error', 'Reward belum memenuhi target dari pembayaran berhasil atau pairing aktif.');
        }

        DB::transaction(function () use ($group, $user, $level, $levelDefinition, $request): void {
            User::query()->lockForUpdate()->findOrFail($user->id);

            $existingAward = AgandaRewardAward::query()
                ->where('member_id', $user->id)
                ->where('group_id', $group->id)
                ->where('reward_level', $level)
                ->lockForUpdate()
                ->exists();

            if ($existingAward) {
                return;
            }

            AgandaRewardAward::create([
                'member_id' => $user->id,
                'group_id' => $group->id,
                'group_code' => $group->kode_group,
                'reward_level' => $level,
                'reward_name' => $levelDefinition['name'],
                'target_value' => $levelDefinition['target'],
                'target_unit' => $levelDefinition['unit'],
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.rewards.show', [$group->id, $user->id])
            ->with('success', 'Reward berhasil diverifikasi dan dicatat pada riwayat.');
    }

    public function history(): View
    {
        $awards = AgandaRewardAward::query()
            ->with(['member', 'group', 'verifiedBy'])
            ->latest('verified_at')
            ->paginate(20);

        return view('admin.rewards.history', compact('awards'));
    }

    public function export(RewardProgressService $progressService): StreamedResponse
    {
        $members = $progressService->dashboard()['members'];

        return response()->streamDownload(function () use ($members): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                'Nama Member',
                'Member ID',
                'Group',
                'Jemaah Berhasil',
                'Pembayaran Berhasil',
                'Level Saat Ini',
                'Reward Berikutnya',
                'Progress',
                'Persentase',
                'Status',
            ]);

            foreach ($members as $row) {
                fputcsv($output, [
                    $row['member']->name,
                    $row['member']->member_id,
                    $row['group_code'],
                    $row['successful_jamaah_count'],
                    $row['paid_payment_total'],
                    $row['next_reward']['level'],
                    $row['next_reward']['name'],
                    $row['next_reward']['progress'].' / '.$row['next_reward']['target'].' '.$row['next_reward']['unit'],
                    $row['progress_percent'].'%',
                    $row['status'],
                ]);
            }

            fclose($output);
        }, 'monitoring-reward-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
