<?php

namespace Tests\Feature;

use App\Actions\Transaction\CancelPendingOrderAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CancelPendingOrderActionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('payments');
        Schema::dropIfExists('orders');

        Schema::create('orders', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('customer_id');
            $table->string('order_status')->default(OrderStatus::PENDING_PAYMENT->value);
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('payment_status')->default(PaymentStatus::PENDING->value);
        });
    }

    public function test_customer_can_cancel_pending_payment_order(): void
    {
        DB::table('orders')->insert([
            'id' => 'order-123',
            'customer_id' => 'customer-123',
            'order_status' => OrderStatus::PENDING_PAYMENT->value,
        ]);

        DB::table('payments')->insert([
            'id' => 'payment-123',
            'order_id' => 'order-123',
            'payment_status' => PaymentStatus::PENDING->value,
        ]);

        $result = app(CancelPendingOrderAction::class)->execute('customer-123', 'order-123');

        $this->assertTrue($result['success']);
        $this->assertSame(OrderStatus::CANCELLED->value, DB::table('orders')->where('id', 'order-123')->value('order_status'));
        $this->assertSame(PaymentStatus::FAILED->value, DB::table('payments')->where('order_id', 'order-123')->value('payment_status'));
    }

    public function test_paid_processing_order_cannot_be_cancelled_by_customer(): void
    {
        DB::table('orders')->insert([
            'id' => 'order-123',
            'customer_id' => 'customer-123',
            'order_status' => OrderStatus::PAID_PROCESSING->value,
        ]);

        $result = app(CancelPendingOrderAction::class)->execute('customer-123', 'order-123');

        $this->assertFalse($result['success']);
        $this->assertSame(OrderStatus::PAID_PROCESSING->value, DB::table('orders')->where('id', 'order-123')->value('order_status'));
    }
}
