<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master Pets Table
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('species');
            $table->enum('rarity', ['common', 'rare', 'epic', 'legendary'])->default('common');
            $table->enum('element', ['fire', 'water', 'nature', 'electric', 'cosmic', 'wind'])->default('nature');
            $table->text('description')->nullable();
            $table->string('color_primary')->default('#4CAF50');
            $table->string('color_secondary')->default('#8BC34A');
            $table->string('model_preset')->default('fox'); // dragon, owl, fox, cat, phoenix, otter
            $table->integer('base_power')->default(10);
            $table->timestamps();
        });

        // User Pets Table
        Schema::create('user_pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pet_id')->nullable()->constrained('pets')->nullOnDelete();
            $table->string('nickname')->nullable();
            $table->enum('status', ['egg', 'ready_to_hatch', 'hatched'])->default('egg');
            $table->enum('egg_tier', ['bronze', 'silver', 'gold', 'crystal'])->default('bronze');
            $table->integer('growth_points')->default(25); // Start with 25 for quick start
            $table->integer('max_growth_points')->default(100);
            $table->integer('level')->default(1);
            $table->integer('experience')->default(0);
            $table->integer('max_experience')->default(100);
            $table->integer('happiness')->default(100);
            $table->integer('energy')->default(100);
            $table->integer('total_hatched_count')->default(0);
            $table->timestamp('hatched_at')->nullable();
            $table->timestamp('last_fed_at')->nullable();
            $table->timestamp('last_petted_at')->nullable();
            $table->timestamps();
        });

        // Pet Logs Table
        Schema::create('pet_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('growth_gained')->default(0);
            $table->string('source_type'); // quiz_completed, video_submitted, daily_bonus, pet_fed
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_logs');
        Schema::dropIfExists('user_pets');
        Schema::dropIfExists('pets');
    }
};
