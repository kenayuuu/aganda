<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GroupMemberCancellationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The PDO SQLite driver is required for group cancellation feature tests.');
        }

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('calon_id')->nullable();
        });

        Schema::create('aganda_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('kode_group');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('package_kegiatan_id');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('calons', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_lengkap');
            $table->timestamps();
        });

        Schema::create('aganda_group_members', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('calon_id');
            $table->unsignedBigInteger('registered_by');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function test_sponsor_can_cancel_a_direct_downline_without_active_children(): void
    {
        $this->createGroup();
        $this->createGroupMember(1, 10, 1);
        $this->createCalon(10);

        $response = $this->actingAs($this->userWithRole('karyawan', 1))
            ->delete(route('aganda.groups.members.cancel', [1, 1]));

        $response->assertRedirectToRoute('aganda.groups.show', 1)
            ->assertSessionHas('success');

        $this->assertDatabaseHas('aganda_group_members', [
            'id' => 1,
            'status' => 'cancelled',
        ]);
    }

    public function test_downline_with_active_children_cannot_be_cancelled(): void
    {
        $this->createGroup();
        $this->createGroupMember(1, 10, 1);
        $this->createGroupMember(2, 11, 2);
        $this->createCalon(10);
        DB::table('users')->insert(['id' => 2, 'calon_id' => 10]);

        $response = $this->actingAs($this->userWithRole('karyawan', 1))
            ->from(route('aganda.groups.show', 1))
            ->delete(route('aganda.groups.members.cancel', [1, 1]));

        $response->assertRedirect(route('aganda.groups.show', 1))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('aganda_group_members', [
            'id' => 1,
            'status' => 'active',
        ]);
    }

    public function test_sponsor_cannot_cancel_another_sponsor_downline(): void
    {
        $this->createGroup();
        $this->createGroupMember(1, 10, 3);
        $this->createCalon(10);

        $this->actingAs($this->userWithRole('karyawan', 1))
            ->delete(route('aganda.groups.members.cancel', [1, 1]))
            ->assertForbidden();
    }

    public function test_member_from_another_group_cannot_be_cancelled_through_this_group(): void
    {
        $this->createGroup();
        $this->createGroup(2, 2);
        $this->createGroupMember(1, 10, 1, 2);
        $this->createCalon(10);

        $this->actingAs($this->userWithRole('admin', 1))
            ->delete(route('aganda.groups.members.cancel', [1, 1]))
            ->assertNotFound();
    }

    private function createGroup(int $groupId = 1, int $ownerId = 1): void
    {
        DB::table('aganda_groups')->insert([
            'id' => $groupId,
            'kode_group' => 'AGR-'.$groupId,
            'owner_id' => $ownerId,
            'package_kegiatan_id' => 1,
            'status' => 'active',
        ]);
    }

    private function createGroupMember(int $id, int $calonId, int $registeredBy, int $groupId = 1): void
    {
        DB::table('aganda_group_members')->insert([
            'id' => $id,
            'group_id' => $groupId,
            'calon_id' => $calonId,
            'registered_by' => $registeredBy,
            'status' => 'active',
        ]);
    }

    private function createCalon(int $calonId): void
    {
        DB::table('calons')->insert([
            'id' => $calonId,
            'nama_lengkap' => 'Calon '.$calonId,
        ]);
    }

    private function userWithRole(string $role, int $userId): User
    {
        return User::factory()
            ->make(['role' => $role])
            ->forceFill(['id' => $userId]);
    }
}
