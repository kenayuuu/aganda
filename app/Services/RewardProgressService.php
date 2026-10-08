<?php

namespace App\Services;

use App\Models\AgandaGroup;
use App\Models\AgandaGroupMember;
use App\Models\AgandaPair;
use App\Models\AgandaRewardAward;
use App\Models\CalonPayment;
use App\Models\User;
use Illuminate\Support\Collection;

class RewardProgressService
{
    public function levels(): array
    {
        return [
            ['level' => 1, 'name' => 'Weekend & Outing', 'target' => 2, 'unit' => 'Teman', 'reward' => 'Weekend & Outing 2H1M di Santika Hotel / Resort pilihan'],
            ['level' => 2, 'name' => 'Tour Malaysia – Singapore', 'target' => 10, 'unit' => 'Pasang', 'reward' => 'Tour Malaysia – Singapore atau Malaysia – Thailand'],
            ['level' => 3, 'name' => 'Tour China Muslim', 'target' => 40, 'unit' => 'Pasang', 'reward' => 'Tour China Muslim 7 Hari'],
            ['level' => 4, 'name' => 'Tour Eropa', 'target' => 100, 'unit' => 'Pasang', 'reward' => 'Tour Eropa 10 Hari'],
            ['level' => 5, 'name' => 'Mobil Listrik', 'target' => 400, 'unit' => 'Pasang', 'reward' => 'Mobil listrik senilai hingga Rp150 Juta'],
            ['level' => 6, 'name' => 'Rumah Sederhana', 'target' => 1000, 'unit' => 'Pasang', 'reward' => 'Rumah sederhana senilai Rp300 Juta'],
            ['level' => 7, 'name' => 'Dana Cash', 'target' => 3000, 'unit' => 'Pasang', 'reward' => 'Dana cash Rp1 Miliar'],
        ];
    }

    /** @return array{members: Collection, levels: Collection, summary: array} */
    public function dashboard(): array
    {
        $membersByGroup = AgandaGroupMember::query()
            ->with(['group', 'calon.user'])
            ->where('status', 'active')
            ->get()
            ->groupBy('group_id');

        $candidateIds = $membersByGroup
            ->flatten(1)
            ->pluck('calon_id')
            ->unique()
            ->values();

        $paidPayments = CalonPayment::query()
            ->whereIn('calon_id', $candidateIds)
            ->where('status', 'paid')
            ->selectRaw('calon_id, COUNT(*) as transaction_count, SUM(amount) as paid_amount')
            ->groupBy('calon_id')
            ->get()
            ->keyBy('calon_id');

        $pairsByGroup = AgandaPair::query()
            ->where('status', 'active')
            ->get()
            ->groupBy('group_id');

        $awards = AgandaRewardAward::query()
            ->get()
            ->keyBy(fn (AgandaRewardAward $award): string => $this->awardKey(
                (int) $award->member_id,
                (int) $award->group_id,
                (int) $award->reward_level
            ));

        $memberRows = collect();
        $levels = collect($this->levels());

        foreach ($membersByGroup as $groupId => $groupMembers) {
            $group = $groupMembers->first()?->group;

            if (! $group) {
                continue;
            }

            $memberUsers = $groupMembers
                ->mapWithKeys(function (AgandaGroupMember $groupMember): array {
                    $member = $groupMember->calon?->user;

                    return $member && $member->role === 'member'
                        ? [(int) $member->id => $member]
                        : [];
                });

            foreach ($memberUsers as $memberId => $member) {
                $directMemberships = $groupMembers->where('registered_by', $memberId)->values();
                $directUsers = $directMemberships
                    ->map(fn (AgandaGroupMember $membership) => $membership->calon?->user)
                    ->filter(fn (?User $user): bool => $user?->role === 'member')
                    ->keyBy('id');

                $secondLevelMemberships = $groupMembers
                    ->filter(fn (AgandaGroupMember $membership): bool => $directUsers->has((int) $membership->registered_by))
                    ->values();

                $networkMemberships = $directMemberships
                    ->concat($secondLevelMemberships)
                    ->unique('calon_id')
                    ->values();

                $directSuccessful = $directMemberships
                    ->filter(fn (AgandaGroupMember $membership): bool => $paidPayments->has($membership->calon_id))
                    ->unique('calon_id')
                    ->values();

                $successfulNetwork = $networkMemberships
                    ->filter(fn (AgandaGroupMember $membership): bool => $paidPayments->has($membership->calon_id))
                    ->unique('calon_id')
                    ->values();

                $successfulUserIds = $successfulNetwork
                    ->map(fn (AgandaGroupMember $membership): ?int => $membership->calon?->user?->id)
                    ->filter()
                    ->map(fn ($id): int => (int) $id)
                    ->flip();

                $validPairCount = ($pairsByGroup->get($groupId, collect()))
                    ->where('user_id', $memberId)
                    ->filter(fn (AgandaPair $pair): bool => $successfulUserIds->has((int) $pair->left_member_id)
                        && $successfulUserIds->has((int) $pair->right_member_id))
                    ->count();

                $levelProgress = $levels->map(function (array $level) use (
                    $memberId,
                    $groupId,
                    $directSuccessful,
                    $validPairCount,
                    $awards
                ): array {
                    $progress = $level['level'] === 1 ? $directSuccessful->count() : $validPairCount;
                    $award = $awards->get($this->awardKey($memberId, (int) $groupId, $level['level']));
                    $isEligible = $progress >= $level['target'];
                    $isAwarded = $award !== null;

                    return [
                        ...$level,
                        'progress' => $progress,
                        'percent' => min(100, (int) round(($progress / $level['target']) * 100)),
                        'eligible' => $isEligible,
                        'awarded' => $isAwarded,
                        'award' => $award,
                        'status' => $isAwarded
                            ? 'Reward Diberikan'
                            : ($isEligible ? 'Menunggu Verifikasi' : $this->progressStatus($progress, $level['target'])),
                    ];
                });

                $nextReward = $levelProgress->first(fn (array $level): bool => ! $level['awarded'])
                    ?? $levelProgress->last();
                $networkPayments = $successfulNetwork->map(fn (AgandaGroupMember $membership) => $paidPayments->get($membership->calon_id));

                $memberRows->push([
                    'member' => $member,
                    'group_id' => (int) $groupId,
                    'group_code' => $group->kode_group,
                    'successful_jamaah_count' => $successfulNetwork->count(),
                    'direct_successful_count' => $directSuccessful->count(),
                    'pair_count' => $validPairCount,
                    'paid_payment_count' => (int) $networkPayments->sum('transaction_count'),
                    'paid_payment_total' => (float) $networkPayments->sum('paid_amount'),
                    'levels' => $levelProgress,
                    'next_reward' => $nextReward,
                    'progress_percent' => $nextReward['percent'],
                    'status' => $this->memberStatus($levelProgress, $nextReward),
                ]);
            }
        }

        $eligibleCount = $memberRows->sum(fn (array $row): int => $row['levels']->where('eligible', true)->count());
        $pendingCount = $memberRows->sum(fn (array $row): int => $row['levels']->where('status', 'Menunggu Verifikasi')->count());

        $levelSummaries = $levels->map(function (array $level) use ($memberRows): array {
            $levelRows = $memberRows->map(fn (array $row): array => $row['levels']->firstWhere('level', $level['level']));
            $eligibleCount = $levelRows->where('eligible', true)->count();
            $chasingCount = $levelRows->filter(fn (array $item): bool => $item['progress'] > 0 && ! $item['eligible'])->count();
            $overallPercent = $levelRows->isEmpty()
                ? 0
                : (int) round($levelRows->sum(fn (array $item): int => $item['percent']) / $levelRows->count());

            return [
                ...$level,
                'chasing_count' => $chasingCount,
                'achieved_count' => $eligibleCount,
                'member_count' => $memberRows->count(),
                'overall_percent' => $overallPercent,
            ];
        });

        $awardedCount = $awards->count();
        $pursuingCount = $memberRows->filter(fn (array $row): bool => $row['status'] === 'Sedang Berjalan'
            || $row['status'] === 'Hampir Tercapai')->count();

        return [
            'members' => $memberRows,
            'levels' => $levelSummaries,
            'summary' => [
                'total_members' => $memberRows->pluck('member.id')->unique()->count(),
                'members_pursuing' => $pursuingCount,
                'achievements' => $eligibleCount,
                'pending_verifications' => $pendingCount,
                'awarded' => $awardedCount,
            ],
        ];
    }

    public function memberDetail(int $groupId, int $memberId): ?array
    {
        $row = $this->dashboard()['members']->first(fn (array $item): bool => $item['group_id'] === $groupId
            && (int) $item['member']->id === $memberId);

        if (! $row) {
            return null;
        }

        $memberships = AgandaGroupMember::query()
            ->with(['calon.packageKegiatan', 'calon.user'])
            ->where('group_id', $groupId)
            ->where('status', 'active')
            ->get();

        $directMemberships = $memberships->where('registered_by', $memberId)->values();
        $directUserIds = $directMemberships
            ->map(fn (AgandaGroupMember $membership) => $membership->calon?->user?->id)
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->all();

        $contributorMemberships = $memberships
            ->filter(fn (AgandaGroupMember $membership): bool => (int) $membership->registered_by === $memberId
                || in_array((int) $membership->registered_by, $directUserIds, true))
            ->unique('calon_id')
            ->values();

        $paymentsByCalon = CalonPayment::query()
            ->whereIn('calon_id', $contributorMemberships->pluck('calon_id'))
            ->with('packageKegiatan')
            ->latest('created_at')
            ->get()
            ->groupBy('calon_id');

        $contributors = $contributorMemberships->map(function (AgandaGroupMember $membership) use ($paymentsByCalon): array {
            $payments = $paymentsByCalon->get($membership->calon_id, collect());
            $successfulPayments = $payments->where('status', 'paid');
            $latestPayment = $payments->first();

            return [
                'calon' => $membership->calon,
                'registered_at' => $membership->created_at,
                'payment_status' => $successfulPayments->isNotEmpty()
                    ? 'Berhasil'
                    : ($latestPayment?->status ? ucfirst($latestPayment->status) : 'Belum membayar'),
                'transaction_count' => $payments->count(),
                'paid_total' => (float) $successfulPayments->sum('amount'),
                'is_counted' => $successfulPayments->isNotEmpty(),
                'depth' => in_array((int) $membership->registered_by, $directUserIds, true) ? 2 : 1,
            ];
        })->sortBy('registered_at')->values();

        $row['contributors'] = $contributors;

        return $row;
    }

    private function awardKey(int $memberId, int $groupId, int $level): string
    {
        return $memberId.':'.$groupId.':'.$level;
    }

    private function progressStatus(int $progress, int $target): string
    {
        if ($progress === 0) {
            return 'Belum Mulai';
        }

        return $progress >= (int) ceil($target * 0.8)
            ? 'Hampir Tercapai'
            : 'Sedang Berjalan';
    }

    private function memberStatus(Collection $levels, array $nextReward): string
    {
        if ($levels->every(fn (array $level): bool => $level['awarded'])) {
            return 'Tercapai';
        }

        if ($nextReward['eligible']) {
            return 'Menunggu Verifikasi';
        }

        return $this->progressStatus($nextReward['progress'], $nextReward['target']);
    }
}
