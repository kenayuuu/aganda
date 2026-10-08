<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class KaryawanFinanceAccessTest extends TestCase
{
    #[DataProvider('karyawanFinancePaths')]
    public function test_guest_is_redirected_to_login_from_karyawan_pages(string $path): void
    {
        $this->get($path)->assertRedirect(route('login'));
    }

    #[DataProvider('unauthorizedRolesAndPaths')]
    public function test_only_karyawan_can_access_karyawan_finance_pages(string $role, string $path): void
    {
        $user = User::factory()
            ->make(['role' => $role])
            ->forceFill(['id' => 1]);

        $this->actingAs($user)->get($path)->assertForbidden();
    }

    public static function karyawanFinancePaths(): array
    {
        return [
            'members' => ['/karyawan/members'],
            'bonus' => ['/karyawan/bonus'],
            'payments' => ['/karyawan/payments'],
        ];
    }

    public static function unauthorizedRolesAndPaths(): array
    {
        return [
            'admin is refused member list' => ['admin', '/karyawan/members'],
            'member is refused bonus page' => ['member', '/karyawan/bonus'],
            'admin is refused payment page' => ['admin', '/karyawan/payments'],
        ];
    }
}
