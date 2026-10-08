<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminCalonRegistrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function createCalonTables(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The PDO SQLite driver is required for calon registration feature tests.');
        }

        Schema::create('package_kegiatans', function (Blueprint $table): void {
            $table->id();
            $table->string('slug');
            $table->string('nama_paket');
            $table->date('tanggal_berlangsung')->nullable();
            $table->string('kategori');
            $table->boolean('is_active')->default(true);
        });

        Schema::create('calons', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_lengkap');
            $table->unsignedSmallInteger('umur')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_paspor')->nullable();
            $table->string('no_kk')->nullable();
            $table->string('no_ktp')->nullable();
            $table->string('akta_kelahiran')->nullable();
            $table->string('no_telepon');
            $table->string('email')->nullable();
            $table->string('jenis_perjalanan');
            $table->date('tanggal_berangkat')->nullable();
            $table->unsignedBigInteger('package_kegiatan_id');
            $table->string('nama_bank', 100)->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function test_admin_can_open_the_calon_registration_form(): void
    {
        $this->createCalonTables();
        $this->createPackage('umroh');

        $this->withoutVite()
            ->actingAs($this->userWithRole('admin'))
            ->get(route('admin.calons.create'))
            ->assertOk()
            ->assertSee('Input Calon Jemaah')
            ->assertSee('Paket kegiatan');
    }

    public function test_admin_can_register_a_calon_jemaah(): void
    {
        $this->createCalonTables();
        $this->createPackage('umroh');

        $response = $this->actingAs($this->userWithRole('admin'))
            ->post(route('admin.calons.store'), [
                'nama_lengkap' => 'Siti Aminah',
                'no_telepon' => '081234567890',
                'email' => 'siti@example.test',
                'jenis_perjalanan' => 'umroh',
                'package_kegiatan_id' => 1,
                'nama_bank' => 'BCA',
                'no_rekening' => '0123456789',
            ]);

        $response->assertRedirectToRoute('admin.calons.create')
            ->assertSessionHas('success', 'Data calon jemaah berhasil disimpan.');

        $this->assertDatabaseHas('calons', [
            'nama_lengkap' => 'Siti Aminah',
            'no_telepon' => '081234567890',
            'email' => 'siti@example.test',
            'jenis_perjalanan' => 'umroh',
            'package_kegiatan_id' => 1,
            'nama_bank' => 'BCA',
            'no_rekening' => '0123456789',
        ]);
    }

    #[DataProvider('incompleteBankDetails')]
    public function test_bank_and_account_number_must_be_provided_together(array $bankDetails, string $errorField): void
    {
        $this->createCalonTables();
        $this->createPackage('umroh');

        $response = $this->actingAs($this->userWithRole('admin'))
            ->from(route('admin.calons.create'))
            ->post(route('admin.calons.store'), array_merge([
                'nama_lengkap' => 'Siti Aminah',
                'no_telepon' => '081234567890',
                'jenis_perjalanan' => 'umroh',
                'package_kegiatan_id' => 1,
            ], $bankDetails));

        $response->assertRedirect(route('admin.calons.create'))
            ->assertSessionHasErrors($errorField);

        $this->assertDatabaseMissing('calons', [
            'nama_lengkap' => 'Siti Aminah',
        ]);
    }

    public function test_other_bank_name_is_saved_as_the_bank_name(): void
    {
        $this->createCalonTables();
        $this->createPackage('umroh');

        $this->actingAs($this->userWithRole('admin'))
            ->post(route('admin.calons.store'), [
                'nama_lengkap' => 'Siti Aminah',
                'no_telepon' => '081234567890',
                'jenis_perjalanan' => 'umroh',
                'package_kegiatan_id' => 1,
                'nama_bank' => 'Lainnya',
                'nama_bank_lainnya' => 'Bank Contoh',
                'no_rekening' => '9988776655',
            ])
            ->assertRedirectToRoute('admin.calons.create');

        $this->assertDatabaseHas('calons', [
            'nama_lengkap' => 'Siti Aminah',
            'nama_bank' => 'Bank Contoh',
            'no_rekening' => '9988776655',
        ]);
    }

    public function test_package_category_must_match_journey_type(): void
    {
        $this->createCalonTables();
        $this->createPackage('umroh');

        $response = $this->actingAs($this->userWithRole('admin'))
            ->from(route('admin.calons.create'))
            ->post(route('admin.calons.store'), [
                'nama_lengkap' => 'Siti Aminah',
                'no_telepon' => '081234567890',
                'jenis_perjalanan' => 'haji',
                'package_kegiatan_id' => 1,
            ]);

        $response->assertRedirect(route('admin.calons.create'))
            ->assertSessionHasErrors('package_kegiatan_id');

        $this->assertDatabaseMissing('calons', [
            'nama_lengkap' => 'Siti Aminah',
        ]);
    }

    #[DataProvider('unauthorizedRoles')]
    public function test_non_admin_roles_cannot_open_the_calon_registration_form(string $role): void
    {
        $this->actingAs($this->userWithRole($role))
            ->get(route('admin.calons.create'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_the_calon_registration_form(): void
    {
        $this->get(route('admin.calons.create'))
            ->assertRedirect(route('login'));
    }

    public static function unauthorizedRoles(): array
    {
        return [
            'karyawan' => ['karyawan'],
            'member' => ['member'],
        ];
    }

    public static function incompleteBankDetails(): array
    {
        return [
            'account number without bank' => [['no_rekening' => '0123456789'], 'nama_bank'],
            'bank without account number' => [['nama_bank' => 'BCA'], 'no_rekening'],
        ];
    }

    private function createPackage(string $category): void
    {
        DB::table('package_kegiatans')->insert([
            'id' => 1,
            'slug' => $category.'-reguler',
            'nama_paket' => ucfirst($category).' Reguler',
            'kategori' => $category,
            'is_active' => true,
        ]);
    }

    private function userWithRole(string $role): User
    {
        return User::factory()
            ->make(['role' => $role])
            ->forceFill(['id' => 1]);
    }
}
