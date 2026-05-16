<?php

namespace App\Actions\Logistics;

use Illuminate\Support\Facades\DB;
use Exception;
use InvalidArgumentException;

class UpdateOrderStatusAction
{
    // Konstanta input
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID            = 'paid';
    public const STATUS_PROCESSING      = 'processing';
    public const STATUS_SHIPPED         = 'shipped';
    public const STATUS_DONE            = 'done';
    public const STATUS_CANCELLED       = 'cancelled';

    // State Machine
    private const VALID_TRANSITIONS = [
        self::STATUS_PENDING_PAYMENT => [self::STATUS_PAID, self::STATUS_CANCELLED],
        self::STATUS_PAID            => [self::STATUS_PROCESSING, self::STATUS_CANCELLED],
        self::STATUS_PROCESSING      => [self::STATUS_SHIPPED],
        self::STATUS_SHIPPED         => [self::STATUS_DONE],
        self::STATUS_DONE            => [], 
        self::STATUS_CANCELLED       => [], 
    ];

    private const DB_MAPPING = [
        self::STATUS_PENDING_PAYMENT => 'PENDING_PAYMENT',
        self::STATUS_PAID            => 'IN_PROCESSING', 
        self::STATUS_PROCESSING      => 'PICKUP_REQUESTED',
        self::STATUS_SHIPPED         => 'ON_DELIVERY',
        self::STATUS_DONE            => 'COMPLETED',
        self::STATUS_CANCELLED       => 'CANCELLED',
    ];

    public function execute(string $orderId, string $newStatus): array
    {
        // Validasi input status
        if (!array_key_exists($newStatus, self::VALID_TRANSITIONS) && $newStatus !== self::STATUS_DONE && $newStatus !== self::STATUS_CANCELLED) {
            throw new InvalidArgumentException("Status '{$newStatus}' tidak valid dalam sistem.");
        }

        // Ambil data pesanan
        $order = DB::table('orders')->where('id', $orderId)->lockForUpdate()->first();

        if (!$order) {
            throw new Exception("Pesanan dengan ID {$orderId} tidak ditemukan.");
        }

        // Terjemahkan status DB lama kembali ke bahasa SCRUM-52
        $currentDbStatus = $order->order_status;
        $currentStatus = array_search($currentDbStatus, self::DB_MAPPING) ?: self::STATUS_PENDING_PAYMENT;

        // Abaikan jika status tidak berubah
        if ($currentStatus === $newStatus) {
            return ['success' => true, 'message' => 'Status pesanan tetap sama.'];
        }

        // Validasi pergerakan status
        $allowedNextStatuses = self::VALID_TRANSITIONS[$currentStatus] ?? [];

        if (!in_array($newStatus, $allowedNextStatuses)) {
            throw new Exception("Transisi ilegal: Tidak dapat mengubah status pesanan dari '{$currentStatus}' menjadi '{$newStatus}'.");
        }

        // Eksekusi pembaruan ke database menggunakan struktur lama
        DB::table('orders')->where('id', $orderId)->update([
            'order_status' => self::DB_MAPPING[$newStatus],
            // Baris 'updated_at' dihapus agar tidak bentrok dengan DDL lama
        ]);

        return [
            'success' => true, 
            'message' => "Status pesanan berhasil diperbarui menjadi {$newStatus}."
        ];
    }
}