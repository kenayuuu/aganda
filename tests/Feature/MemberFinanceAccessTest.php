<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MemberFinanceAccessTest extends TestCase
{
    #[DataProvider('memberFinanceUrls')]
    public function test_guest_is_redirected_to_login_from_member_finance_pages(string $url): void
    {
        $this->get($url)->assertRedirect(route('login'));
    }

    #[DataProvider('unauthorizedRoles')]
    public function test_non_member_roles_are_forbidden_from_member_finance_pages(string $role, string $url): void
    {
        $user = User::factory()
            ->make(['role' => $role])
            ->forceFill(['id' => 1]);

        $this->actingAs($user)->get($url)->assertForbidden();
    }

    public static function memberFinanceUrls(): array
    {
        return [
            'bonus' => ['/member/bonus'],
            'payments' => ['/member/payments'],
        ];
    }

    public static function unauthorizedRoles(): array
    {
        return [
            'admin cannot use member pages' => ['admin', '/member/bonus'],
            'karyawan cannot use member payments' => ['karyawan', '/member/payments'],
        ];
    }
}
