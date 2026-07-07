<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->after('name')->nullable();
            $table->foreignId('division_id')->nullable()->after('remember_token')->constrained('divisions')->nullOnDelete();
            $table->enum('role', ['hr', 'employee'])->default('employee')->after('division_id');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
            $table->string('phone')->nullable()->after('status');
            $table->text('address')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn([
                'full_name',
                'division_id',
                'role',
                'status',
                'phone',
                'address',
                'avatar'
            ]);
        });
    }
};
