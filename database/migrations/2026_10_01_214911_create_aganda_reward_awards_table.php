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
        Schema::create('aganda_reward_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('aganda_groups')->nullOnDelete();
            $table->string('group_code', 255);
            $table->unsignedTinyInteger('reward_level');
            $table->string('reward_name', 255);
            $table->unsignedInteger('target_value');
            $table->string('target_unit', 30);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at');
            $table->timestamps();

            $table->unique(['member_id', 'group_id', 'reward_level'], 'aganda_reward_awards_member_group_level_unique');
            $table->index(['reward_level', 'verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aganda_reward_awards');
    }
};
