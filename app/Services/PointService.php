<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PointService
{
    /**
     * Saldo poin user saat ini.
     */
    public function balance(User $user): int
    {
        return (int) $user->points;
    }

    /**
     * Tambah poin (hadiah). Mengembalikan record ledger transaksi.
     */
    public function earn(User $user, int $amount, string $type, string $description = ''): PointTransaction
    {
        $amount = max(0, $amount);

        return DB::transaction(function () use ($user, $amount, $type, $description) {
            // Ambil baris terbaru + kunci agar saldo konsisten saat bersamaan
            $fresh = User::whereKey($user->id)->lockForUpdate()->first() ?? $user;

            $newBalance = (int) $fresh->points + $amount;
            $fresh->points = $newBalance;
            $fresh->save();

            // Sinkronkan juga instance di memori (auth user)
            $user->points = $newBalance;

            return PointTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'balance_after' => $newBalance,
                'description' => $description ?: "Mendapatkan +{$amount} poin",
            ]);
        });
    }

    /**
     * Kurangi poin (belanja). Gagal (success=false) jika saldo kurang.
     *
     * @return array{success: bool, message: string, balance: int, transaction: ?PointTransaction}
     */
    public function spend(User $user, int $amount, string $type, string $description = ''): array
    {
        $amount = max(0, $amount);

        return DB::transaction(function () use ($user, $amount, $type, $description) {
            $fresh = User::whereKey($user->id)->lockForUpdate()->first() ?? $user;
            $balance = (int) $fresh->points;

            if ($balance < $amount) {
                return [
                    'success' => false,
                    'message' => "Poin tidak cukup! Butuh {$amount} poin, saldo kamu {$balance}.",
                    'balance' => $balance,
                    'transaction' => null,
                ];
            }

            $newBalance = $balance - $amount;
            $fresh->points = $newBalance;
            $fresh->save();
            $user->points = $newBalance;

            $tx = PointTransaction::create([
                'user_id' => $user->id,
                'amount' => -$amount,
                'type' => $type,
                'balance_after' => $newBalance,
                'description' => $description ?: "Membelanjakan {$amount} poin",
            ]);

            return [
                'success' => true,
                'message' => "-{$amount} poin berhasil dibelanjakan.",
                'balance' => $newBalance,
                'transaction' => $tx,
            ];
        });
    }

    /**
     * Riwayat transaksi terbaru.
     */
    public function history(User $user, int $limit = 30)
    {
        return PointTransaction::where('user_id', $user->id)
            ->latest()
            ->take($limit)
            ->get();
    }
}
