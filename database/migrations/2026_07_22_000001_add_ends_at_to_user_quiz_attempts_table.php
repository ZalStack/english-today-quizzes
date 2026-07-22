<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_quiz_attempts', function (Blueprint $table) {
            $table->dateTime('ends_at')->nullable()->after('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_quiz_attempts', function (Blueprint $table) {
            $table->dropColumn('ends_at');
        });
    }
};
