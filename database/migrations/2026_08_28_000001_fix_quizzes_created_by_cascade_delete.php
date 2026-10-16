<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('quizzes') || ! Schema::hasColumn('quizzes', 'created_by')) {
            return;
        }

        // 1) Kolom created_by dibuat nullable agar kuis tetap ada
        //    walaupun akun pembuatnya kelak dihapus.
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->change();
        });

        // 2) Amankan baris "yatim" (created_by yang sudah tidak ada di tabel users)
        //    supaya pemasangan FK baru tidak gagal karena data lama.
        DB::table('quizzes')
            ->whereNotNull('created_by')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('users')
                    ->whereColumn('users.id', 'quizzes.created_by');
            })
            ->update(['created_by' => null]);

        // 3) Lepas foreign key lama lalu pasang ulang: users.id ON DELETE SET NULL.
        //    Nama FK di production bisa berbeda dari default Laravel
        //    (misal hasil restore dump / edit manual via phpMyAdmin),
        //    sehingga dropForeign(['created_by']) yang hardcode nama bisa gagal
        //    dengan error "Can't DROP FOREIGN KEY ... check that it exists".
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            // SQLite tidak punya ALTER ... DROP FOREIGN KEY;
            // dropForeign by columns menghapus FK kolom tersebut saat tabel di-rebuild.
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        } else {
            // MySQL / MariaDB / lainnya: lepas SEMUA FK pada kolom created_by
            // berapa pun jumlah dan apa pun namanya.
            foreach ($this->foreignKeysOn('quizzes', 'created_by') as $foreignKeyName) {
                Schema::table('quizzes', function (Blueprint $table) use ($foreignKeyName) {
                    $table->dropForeign($foreignKeyName);
                });
            }

            Schema::table('quizzes', function (Blueprint $table) {
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('quizzes') || ! Schema::hasColumn('quizzes', 'created_by')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            });
        } else {
            foreach ($this->foreignKeysOn('quizzes', 'created_by') as $foreignKeyName) {
                Schema::table('quizzes', function (Blueprint $table) use ($foreignKeyName) {
                    $table->dropForeign($foreignKeyName);
                });
            }

            Schema::table('quizzes', function (Blueprint $table) {
                $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable(false)->change();
        });
    }

    /**
     * Nama-nama foreign key pada satu kolom tertentu, berapa pun jumlah & namanya.
     *
     * @return list<string>
     */
    private function foreignKeysOn(string $tableName, string $columnName): array
    {
        return collect(Schema::getForeignKeys($tableName))
            ->filter(fn (array $foreignKey) => in_array(
                strtolower($columnName),
                array_map('strtolower', $foreignKey['columns'] ?? []),
                true
            ))
            ->pluck('name')
            ->filter()
            ->values()
            ->all();
    }
};
