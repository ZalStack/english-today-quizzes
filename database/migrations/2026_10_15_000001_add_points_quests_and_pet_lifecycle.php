<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Saldo poin employee + penanda welcome pack pernah diklaim
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('points')->default(0)->after('avatar');
            $table->timestamp('welcome_pack_claimed_at')->nullable()->after('points');
        });

        // Ledger transaksi poin (+ masuk / - keluar)
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('amount');
            $table->string('type'); // welcome_pack, quiz_first_attempt, video_submitted, daily_quest, shop_purchase, tool_use
            $table->unsignedBigInteger('balance_after')->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        // Tahap hidup, generasi & meteran keretakan telur
        Schema::table('user_pets', function (Blueprint $table) {
            $table->enum('life_stage', ['egg', 'baby', 'juvenile', 'adult'])->default('egg')->after('status');
            $table->unsignedInteger('generation')->default(1)->after('egg_tier');
            $table->unsignedInteger('crack_points')->default(0)->after('growth_points');
        });

        // Klaim misi harian (sekali per hari per quest)
        Schema::create('daily_quest_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('quest_key');
            $table->date('claimed_on');
            $table->timestamps();
            $table->unique(['user_id', 'quest_key', 'claimed_on']);
        });

        // Koleksi / Hall of Fame pet yang sudah dewasa & beranak
        Schema::create('user_pet_archive', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pet_id')->nullable()->constrained('pets')->nullOnDelete();
            $table->string('nickname')->nullable();
            $table->string('species')->nullable();
            $table->string('rarity')->default('common');
            $table->string('element')->default('nature');
            $table->string('model_preset')->default('fox');
            $table->string('color_primary')->nullable();
            $table->string('color_secondary')->nullable();
            $table->unsignedInteger('generation')->default(1);
            $table->unsignedInteger('final_level')->default(1);
            $table->timestamp('hatched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pet_archive');
        Schema::dropIfExists('daily_quest_claims');

        Schema::table('user_pets', function (Blueprint $table) {
            $table->dropColumn(['life_stage', 'generation', 'crack_points']);
        });

        Schema::dropIfExists('point_transactions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['points', 'welcome_pack_claimed_at']);
        });
    }
};
