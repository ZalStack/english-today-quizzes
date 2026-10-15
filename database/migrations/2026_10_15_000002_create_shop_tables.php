<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Katalog toko: alat pecah telur & makanan perawatan
        Schema::create('shop_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('category', ['tool', 'food'])->default('tool');
            $table->text('description')->nullable();
            $table->string('icon')->default('🎁');
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('power')->default(0);      // daya retak (tool)
            $table->unsignedInteger('uses')->default(1);       // isi per pembelian
            $table->unsignedInteger('effect_exp')->default(0);      // EXP pet (food)
            $table->unsignedInteger('effect_happiness')->default(0);
            $table->unsignedInteger('effect_energy')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Inventaris milik user (quantity = sisa pakai/porsi)
        Schema::create('user_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'shop_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_items');
        Schema::dropIfExists('shop_items');
    }
};
