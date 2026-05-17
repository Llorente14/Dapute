<?php

namespace App\Actions\Payment;

use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class GetMidtransSnapTokenAction
{
    /**
     * Meminta Snap Token dari Midtrans berdasarkan Order ID
     */
    public function execute(string $orderId): array
    {
        // 1. Setup Konfigurasi Midtrans SDK sesuai .env constraint
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized = filter_var(env('MIDTRANS_SANITIZE', false), FILTER_VALIDATE_BOOLEAN);
        Config::$is3ds = filter_var(env('MIDTRANS_ENABLE_3DS', true), FILTER_VALIDATE_BOOLEAN);

        try {
            // 2. Ambil data order bergabung dengan data customer dari tabel users
            $order = DB::table('orders')
                ->join('users', 'orders.customer_id', '=', 'users.id')
                ->where('orders.id', $orderId)
                ->select('orders.*', 'users.full_name as customer_name', 'users.email as customer_email', 'users.phone_number as customer_phone')
                ->first();

            if (!$order) {
                return ['success' => false, 'message' => 'Pesanan tidak ditemukan.'];
            }

            // 3. CONSTRAINT: Cek apakah order sudah memiliki token aktif (Database Check)
            if (isset($order->snap_token) && !empty($order->snap_token)) {
                return ['success' => true, 'snap_token' => $order->snap_token];
            }

            // FALLBACK CONSTRAINT: Cek via Cache Lock/Storage jika kolom DB belum siap
            $cachedToken = Cache::get("midtrans_token_order_{$orderId}");
            if ($cachedToken) {
                return ['success' => true, 'snap_token' => $cachedToken];
            }

            // 4. Merakit Payload Transaksi sesuai kriteria Midtrans Snap
            $transactionDetails = [
                'order_id'     => $order->id,
                'gross_amount' => (int) $order->total_payment, // Total rupiah (subtotal + ongkir)
            ];

            $customerDetails = [
                'first_name' => $order->customer_name,
                'email'      => $order->customer_email,
                'phone'      => $order->customer_phone,
            ];

            $payload = [
                'transaction_details' => $transactionDetails,
                'customer_details'    => $customerDetails,
            ];

            // 5. Eksekusi Request ke Midtrans API
            $snapToken = Snap::getSnapToken($payload);

            // 6. Amankan Token ke Database & Cache agar tidak terjadi redundansi token
            Cache::put("midtrans_token_order_{$orderId}", $snapToken, now()->addHours(24));
            $this->ensurePendingPayment($order, $snapToken);
            
            try {
                DB::table('orders')->where('id', $orderId)->update([
                    'snap_token' => $snapToken
                ]);
            } catch (\Exception $dbEx) {
                // Mencegah crash jika kolom snap_token belum dimigrasi di Supabase
                Log::warning("Kolom snap_token tidak ditemukan di tabel orders, token disimpan di cache.");
            }

            return [
                'success'    => true, 
                'snap_token' => $snapToken
            ];

        } catch (\Exception $e) {
            // CONSTRAINT: Jika Midtrans gagal, status order tetap 'PENDING_PAYMENT' dan di-handle agar tidak crash
            Log::error("Midtrans Snap Token Generation Failed: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal memicu sistem pembayaran luar. Silakan coba beberapa saat lagi.',
                'error'   => $e->getMessage()
            ];
        }
    }

    private function ensurePendingPayment(object $order, string $snapToken): void
    {
        try {
            $existingPayment = DB::table('payments')
                ->where('order_id', $order->id)
                ->first();

            if ($existingPayment && ($existingPayment->payment_status ?? null) === PaymentStatus::PAID->value) {
                return;
            }

            $data = [
                'payment_method' => 'midtrans',
                'payment_status' => PaymentStatus::PENDING->value,
                'amount' => (int) $order->total_payment,
                'snap_token' => $snapToken,
            ];

            if ($existingPayment) {
                DB::table('payments')
                    ->where('id', $existingPayment->id)
                    ->update($data);

                return;
            }

            DB::table('payments')->insert($data + [
                'id' => (string) Str::uuid(),
                'order_id' => $order->id,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to prepare pending payment row: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
            ]);
        }
    }
}
