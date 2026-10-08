<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RewardDashboardTest extends TestCase
{
    #[DataProvider('allowedRoles')]
    public function test_allowed_roles_can_see_the_reward_dashboard_and_all_levels(string $role): void
    {
        $user = User::factory()
            ->make(['role' => $role])
            ->forceFill(['id' => 1]);

        $this->withoutVite()
            ->actingAs($user)
            ->get(route('aganda.rewards.index'))
            ->assertOk()
            ->assertSee('Total Reward')
            ->assertSee('Reward Tercapai')
            ->assertSee('Ajak 2 Teman')
            ->assertSee('Weekend &amp; Outing', false)
            ->assertSee('Tour Malaysia')
            ->assertSee('Tour China Muslim')
            ->assertSee('Tour Eropa')
            ->assertSee('Mobil Listrik')
            ->assertSee('Rumah Sederhana')
            ->assertSee('Dana Cash')
            ->assertSee('Lihat Detail');
    }

    public static function allowedRoles(): array
    {
        return [
            'admin' => ['admin'],
            'karyawan' => ['karyawan'],
            'member' => ['member'],
        ];
    }
}
