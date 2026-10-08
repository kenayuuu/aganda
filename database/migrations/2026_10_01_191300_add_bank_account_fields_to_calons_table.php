<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('calons')) {
            return;
        }

        Schema::table('calons', function (Blueprint $table) {
            if (! Schema::hasColumn('calons', 'nama_bank')) {
                $table->string('nama_bank', 100)->nullable();
            }

            if (! Schema::hasColumn('calons', 'no_rekening')) {
                $table->string('no_rekening', 50)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('calons')) {
            return;
        }

        $columns = array_values(array_filter(
            ['nama_bank', 'no_rekening'],
            fn (string $column): bool => Schema::hasColumn('calons', $column)
        ));

        if ($columns === []) {
            return;
        }

        Schema::table('calons', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
        });
    }
};
