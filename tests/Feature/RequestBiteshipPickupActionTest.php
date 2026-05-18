<?php

namespace Tests\Feature;

use App\Actions\Logistics\RequestBiteshipPickupAction;
use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RequestBiteshipPickupActionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('products');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('role')->default('customer');
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_status');
            $table->string('biteship_order_id')->nullable();
            $table->string('tracking_id')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->integer('weight_gram')->default(500);
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('product_id');
            $table->string('cake_name_snapshot');
            $table->integer('price_snapshot');
            $table->integer('quantity');
        });

        Schema::create('order_addresses', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('postal_code')->nullable();
        });

        DB::table('users')->insert([
            ['id' => 'admin-123', 'role' => 'admin'],
        ]);

        $this->seedPickupOrder();

        Config::set('services.biteship.api_key', 'test-key');
        Config::set('services.biteship.base_url', 'https://api.biteship.test/v1');
    }

    public function test_insufficient_balance_can_be_simulated_outside_production(): void
    {
        Config::set('services.biteship.simulate_insufficient_balance', true);
        Http::fake([
            'https://api.biteship.test/v1/orders' => Http::response([
                'success' => false,
                'error' => 'No sufficient balance to call orders API. Please top up your balance',
            ]),
        ]);

        $this->actingAs($this->authUser('admin-123'));

        $result = app(RequestBiteshipPickupAction::class)->execute('order-123', 'jne', 'reg');
        $order = DB::table('orders')->where('id', 'order-123')->first();

        $this->assertTrue($result['success']);
        $this->assertTrue($result['simulated']);
        $this->assertSame(OrderStatus::ON_DELIVERY->value, $order->order_status);
        $this->assertStringStartsWith('SIMULATED-BITESHIP-', $order->biteship_order_id);
        $this->assertStringStartsWith('SIMULATED-JNE-', $order->tracking_id);
    }

    public function test_insufficient_balance_fails_when_simulation_disabled_for_production(): void
    {
        Config::set('services.biteship.simulate_insufficient_balance', false);
        Http::fake([
            'https://api.biteship.test/v1/orders' => Http::response([
                'success' => false,
                'error' => 'No sufficient balance to call orders API. Please top up your balance',
            ]),
        ]);

        $this->actingAs($this->authUser('admin-123'));

        $result = app(RequestBiteshipPickupAction::class)->execute('order-123', 'jne', 'reg');
        $order = DB::table('orders')->where('id', 'order-123')->first();

        $this->assertFalse($result['success']);
        $this->assertSame('Saldo Biteship tidak cukup. Top up saldo terlebih dahulu.', $result['message']);
        $this->assertSame(OrderStatus::PICKUP_REQUESTED->value, $order->order_status);
        $this->assertNull($order->biteship_order_id);
        $this->assertNull($order->tracking_id);
    }

    private function seedPickupOrder(): void
    {
        DB::table('orders')->insert([
            'id' => 'order-123',
            'order_status' => OrderStatus::PICKUP_REQUESTED->value,
            'biteship_order_id' => null,
            'tracking_id' => null,
        ]);

        DB::table('products')->insert([
            'id' => 'product-123',
            'weight_gram' => 500,
        ]);

        DB::table('order_items')->insert([
            'id' => 'item-123',
            'order_id' => 'order-123',
            'product_id' => 'product-123',
            'cake_name_snapshot' => 'Chocolate Cake',
            'price_snapshot' => 100000,
            'quantity' => 1,
        ]);

        DB::table('order_addresses')->insert([
            'id' => 'address-123',
            'order_id' => 'order-123',
            'recipient_name' => 'Customer User',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Testing No. 1',
            'postal_code' => '11440',
        ]);
    }

    private function authUser(string $id): User
    {
        $user = new User();
        $user->id = $id;
        $user->role = DB::table('users')->where('id', $id)->value('role');
        $user->exists = true;

        return $user;
    }
}
