<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrderHistoryPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('products');
        Schema::dropIfExists('orders');

        Schema::create('orders', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('customer_id');
            $table->timestamp('order_date')->nullable();
            $table->integer('subtotal_amount');
            $table->integer('shipping_fee')->default(0);
            $table->integer('total_payment');
            $table->text('notes')->nullable();
            $table->string('order_status')->default(OrderStatus::PENDING_PAYMENT->value);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('biteship_order_id')->nullable();
            $table->string('tracking_id')->nullable();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('image_url')->nullable();
            $table->integer('weight_grams')->default(500);
        });

        Schema::create('carts', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->string('product_id');
            $table->string('cake_name_snapshot');
            $table->integer('price_snapshot');
            $table->string('image_url_snapshot')->nullable();
            $table->integer('quantity');
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('product_id')->nullable();
            $table->string('cake_name_snapshot');
            $table->integer('price_snapshot');
            $table->integer('quantity');
            $table->integer('subtotal');
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('payment_status')->default(PaymentStatus::PENDING->value);
        });

        Schema::create('order_addresses', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('city')->nullable();
        });
    }

    public function test_order_history_page_renders_user_orders(): void
    {
        $user = new User();
        $user->id = 'customer-123';
        $user->exists = true;

        DB::table('orders')->insert([
            'id' => 'order-12345678',
            'customer_id' => 'customer-123',
            'order_date' => now(),
            'subtotal_amount' => 100000,
            'shipping_fee' => 20000,
            'total_payment' => 122500,
            'order_status' => OrderStatus::PAID_PROCESSING->value,
            'tracking_id' => 'TRACK-123',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            'id' => 'product-123',
            'image_url' => 'https://example.test/cake.jpg',
        ]);

        DB::table('order_items')->insert([
            'id' => 'item-123',
            'order_id' => 'order-12345678',
            'product_id' => 'product-123',
            'cake_name_snapshot' => 'Kue See',
            'price_snapshot' => 100000,
            'quantity' => 1,
            'subtotal' => 100000,
        ]);

        DB::table('payments')->insert([
            'id' => 'payment-123',
            'order_id' => 'order-12345678',
            'payment_status' => PaymentStatus::PAID->value,
        ]);

        DB::table('order_addresses')->insert([
            'id' => 'address-123',
            'order_id' => 'order-12345678',
            'city' => 'Jakarta',
        ]);

        $this->actingAs($user)
            ->get('/order')
            ->assertOk()
            ->assertSeeText('Order')
            ->assertSeeText('History')
            ->assertSee('Kue See')
            ->assertSee('Paid Processing')
            ->assertSee('Track Package');
    }
}
