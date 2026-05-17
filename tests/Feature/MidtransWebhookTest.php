<?php

namespace Tests\Feature;

use App\Actions\Transaction\ProcessMidtransWebhookAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Jobs\ProcessMidtransWebhookJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('payments');
        Schema::dropIfExists('orders');

        Schema::create('orders', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('customer_id');
            $table->integer('subtotal_amount');
            $table->integer('shipping_fee')->default(0);
            $table->integer('total_payment');
            $table->string('order_status')->default(OrderStatus::PENDING_PAYMENT->value);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('transaction_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default(PaymentStatus::PENDING->value);
            $table->integer('amount');
            $table->text('snap_token')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function test_valid_midtrans_webhook_returns_ok_and_dispatches_job(): void
    {
        config(['services.midtrans.server_key' => 'midtrans-secret']);
        Bus::fake();

        $payload = [
            'order_id' => 'order-123',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'transaction_status' => 'settlement',
        ];
        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . 'midtrans-secret'
        );

        $this->postJson('/api/webhook/midtrans', $payload)
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        Bus::assertDispatchedSync(ProcessMidtransWebhookJob::class, fn (ProcessMidtransWebhookJob $job) => $job->payload === $payload);
    }

    public function test_invalid_midtrans_signature_returns_forbidden_and_dispatches_no_job(): void
    {
        config(['services.midtrans.server_key' => 'midtrans-secret']);
        Bus::fake();

        $this->postJson('/api/webhook/midtrans', [
            'order_id' => 'order-123',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'bad-signature',
        ])->assertForbidden();

        Bus::assertNotDispatched(ProcessMidtransWebhookJob::class);
    }

    public function test_settlement_updates_payment_and_order_status(): void
    {
        $this->seedPendingOrderAndPayment('order-123');

        app(ProcessMidtransWebhookAction::class)->execute([
            'order_id' => 'order-123',
            'transaction_status' => 'settlement',
            'transaction_id' => 'midtrans-transaction-1',
        ]);

        $payment = DB::table('payments')->where('order_id', 'order-123')->first();
        $order = DB::table('orders')->where('id', 'order-123')->first();

        $this->assertSame(PaymentStatus::PAID->value, $payment->payment_status);
        $this->assertSame('midtrans-transaction-1', $payment->transaction_id);
        $this->assertNotNull($payment->paid_at);
        $this->assertSame(OrderStatus::PAID_PROCESSING->value, $order->order_status);
    }

    public function test_expire_updates_payment_expired_and_order_cancelled(): void
    {
        $this->seedPendingOrderAndPayment('order-123');

        app(ProcessMidtransWebhookAction::class)->execute([
            'order_id' => 'order-123',
            'transaction_status' => 'expire',
            'transaction_id' => 'midtrans-transaction-2',
        ]);

        $payment = DB::table('payments')->where('order_id', 'order-123')->first();
        $order = DB::table('orders')->where('id', 'order-123')->first();

        $this->assertSame(PaymentStatus::EXPIRED->value, $payment->payment_status);
        $this->assertSame('midtrans-transaction-2', $payment->transaction_id);
        $this->assertNotNull($payment->expired_at);
        $this->assertSame(OrderStatus::CANCELLED->value, $order->order_status);
    }

    public function test_paid_payment_is_idempotent_and_not_downgraded(): void
    {
        $this->seedPendingOrderAndPayment('order-123');

        DB::table('payments')->where('order_id', 'order-123')->update([
            'payment_status' => PaymentStatus::PAID->value,
            'paid_at' => now(),
        ]);
        DB::table('orders')->where('id', 'order-123')->update([
            'order_status' => OrderStatus::PAID_PROCESSING->value,
        ]);

        app(ProcessMidtransWebhookAction::class)->execute([
            'order_id' => 'order-123',
            'transaction_status' => 'failure',
            'transaction_id' => 'late-failure',
        ]);

        $payment = DB::table('payments')->where('order_id', 'order-123')->first();
        $order = DB::table('orders')->where('id', 'order-123')->first();

        $this->assertSame(PaymentStatus::PAID->value, $payment->payment_status);
        $this->assertNull($payment->transaction_id);
        $this->assertSame(OrderStatus::PAID_PROCESSING->value, $order->order_status);
    }

    private function seedPendingOrderAndPayment(string $orderId): void
    {
        DB::table('orders')->insert([
            'id' => $orderId,
            'customer_id' => 'customer-123',
            'subtotal_amount' => 10000,
            'shipping_fee' => 0,
            'total_payment' => 10000,
            'order_status' => OrderStatus::PENDING_PAYMENT->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('payments')->insert([
            'id' => 'payment-' . $orderId,
            'order_id' => $orderId,
            'payment_method' => 'midtrans',
            'payment_status' => PaymentStatus::PENDING->value,
            'amount' => 10000,
            'created_at' => now(),
        ]);
    }
}
