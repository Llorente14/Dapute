<?php

namespace Tests\Feature;

use App\Actions\Transaction\FetchOrderDetailAction;
use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FetchOrderDetailActionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('order_trackings');
        Schema::dropIfExists('order_addresses');
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

        Schema::create('order_addresses', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
        });
    }

    public function test_fetch_order_detail_loads_tracking_events_newest_first(): void
    {
        $this->seedOrderDetail();

        Schema::create('order_trackings', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('status');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        DB::table('order_trackings')->insert([
            [
                'id' => 'tracking-old',
                'order_id' => 'order-123',
                'status' => 'PICKUP_REQUESTED',
                'description' => 'Courier pickup requested.',
                'created_at' => '2026-05-17 10:00:00',
                'updated_at' => '2026-05-17 10:00:00',
            ],
            [
                'id' => 'tracking-new',
                'order_id' => 'order-123',
                'status' => 'ON_DELIVERY',
                'description' => 'Courier is delivering package.',
                'created_at' => '2026-05-17 12:00:00',
                'updated_at' => '2026-05-17 12:00:00',
            ],
        ]);

        $result = app(FetchOrderDetailAction::class)->execute('customer-123', 'order-123');

        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['trackings']);
        $this->assertSame('ON_DELIVERY', $result['trackings'][0]['status']);
        $this->assertSame('On Delivery', $result['trackings'][0]['label']);
        $this->assertSame('Courier is delivering package.', $result['trackings'][0]['description']);
    }

    public function test_fetch_order_detail_still_works_without_tracking_table(): void
    {
        $this->seedOrderDetail();

        $result = app(FetchOrderDetailAction::class)->execute('customer-123', 'order-123');

        $this->assertTrue($result['success']);
        $this->assertSame([], $result['trackings']);
    }

    public function test_order_detail_page_renders_tracking_timeline(): void
    {
        $this->seedOrderDetail();
        $this->createTrackingTable();

        DB::table('order_trackings')->insert([
            'id' => 'tracking-new',
            'order_id' => 'order-123',
            'status' => 'ON_DELIVERY',
            'description' => 'Courier is delivering package.',
            'created_at' => '2026-05-17 12:00:00',
            'updated_at' => '2026-05-17 12:00:00',
        ]);

        $user = new User();
        $user->id = 'customer-123';
        $user->exists = true;

        $this->actingAs($user)
            ->get('/order/order-123')
            ->assertOk()
            ->assertSeeText('Tracking Timeline')
            ->assertSeeText('TRACK-123')
            ->assertSeeText('On Delivery')
            ->assertSeeText('Courier is delivering package.');
    }

    public function test_order_detail_page_uses_status_fallback_when_tracking_empty(): void
    {
        $this->seedOrderDetail(OrderStatus::DELIVERED->value);

        $user = new User();
        $user->id = 'customer-123';
        $user->exists = true;

        $this->actingAs($user)
            ->get('/order/order-123')
            ->assertOk()
            ->assertSeeText('Tracking Timeline')
            ->assertSeeText('TRACK-123')
            ->assertSeeText('Delivered')
            ->assertSeeText('Package has been marked as delivered.')
            ->assertDontSeeText('Courier Has Not Picked Up Package Yet');
    }

    private function seedOrderDetail(string $status = OrderStatus::ON_DELIVERY->value): void
    {
        DB::table('orders')->insert([
            'id' => 'order-123',
            'customer_id' => 'customer-123',
            'order_date' => now(),
            'subtotal_amount' => 100000,
            'shipping_fee' => 20000,
            'total_payment' => 122500,
            'order_status' => $status,
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
            'order_id' => 'order-123',
            'product_id' => 'product-123',
            'cake_name_snapshot' => 'Kue See',
            'price_snapshot' => 100000,
            'quantity' => 1,
            'subtotal' => 100000,
        ]);
    }

    private function createTrackingTable(): void
    {
        Schema::create('order_trackings', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('status');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
}
