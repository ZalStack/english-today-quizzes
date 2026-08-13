<?php
// database/migrations/2026_08_13_000001_add_material_fields_to_video_challenges_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('video_challenges', function (Blueprint $table) {
            $table->string('material_link')->nullable()->after('description');
            $table->string('material_title')->nullable()->after('material_link');
            $table->string('kisi_kisi_link')->nullable()->after('material_title');
            $table->string('kisi_kisi_title')->nullable()->after('kisi_kisi_link');
        });
    }

    public function down(): void
    {
        Schema::table('video_challenges', function (Blueprint $table) {
            $table->dropColumn(['material_link', 'material_title', 'kisi_kisi_link', 'kisi_kisi_title']);
        });
    }
};
