<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Actions\Logistics\ManualShipmentAction;
use App\Actions\Logistics\UpdateOrderStatusAction;
use App\Livewire\Admin\OrderQueue;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class AdminOrderQueueTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('role')->default('customer');
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('customer_id');
            $table->timestamp('order_date')->nullable();
            $table->integer('total_payment');
            $table->string('order_status')->default(OrderStatus::PENDING_PAYMENT->value);
            $table->string('biteship_order_id')->nullable();
            $table->string('tracking_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
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

        Schema::create('order_items', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('cake_name_snapshot');
            $table->integer('quantity');
            $table->integer('subtotal');
        });

        DB::table('users')->insert([
            ['id' => 'owner-123', 'full_name' => 'Owner User', 'email' => 'owner@example.test', 'role' => 'owner'],
            ['id' => 'staff-123', 'full_name' => 'Staff User', 'email' => 'staff@example.test', 'role' => 'staff'],
            ['id' => 'customer-123', 'full_name' => 'Customer User', 'email' => 'customer@example.test', 'role' => 'customer'],
        ]);
    }

    public function test_staff_order_queue_route_renders_active_orders(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PAID_PROCESSING->value);

        $this->actingAs($this->authUser('staff-123'))
            ->get('/admin/orders')
            ->assertOk()
            ->assertSee('Order Queue')
            ->assertSee('Customer User')
            ->assertSee('Paid Processing')
            ->assertSee('Actions')
            ->assertSee('Available')
            ->assertSee('Unavailable')
            ->assertSee('Ready to Ship')
            ->assertSee('Request Pickup')
            ->assertSee('Manual Shipment');
    }

    public function test_owner_can_access_order_queue_route(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PAID_PROCESSING->value);

        $this->actingAs($this->authUser('owner-123'))
            ->get('/admin/orders')
            ->assertOk()
            ->assertSee('Order Queue');
    }

    public function test_customer_access_to_admin_order_queue_returns_not_found(): void
    {
        $this->actingAs($this->authUser('customer-123'))
            ->get('/admin/orders')
            ->assertNotFound();
    }

    public function test_guest_access_to_admin_users_returns_not_found(): void
    {
        $this->get('/admin/users')->assertNotFound();
    }

    public function test_staff_cannot_access_owner_only_admin_pages(): void
    {
        $this->actingAs($this->authUser('staff-123'))
            ->get('/admin/users')
            ->assertNotFound();

        $this->actingAs($this->authUser('staff-123'))
            ->get('/admin/products')
            ->assertNotFound();

        $this->actingAs($this->authUser('staff-123'))
            ->get('/admin/reports')
            ->assertNotFound();
    }

    public function test_order_queue_filters_expands_and_updates_status(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PAID_PROCESSING->value);
        $this->seedOrder('order-2', 'customer-123', OrderStatus::PENDING_PAYMENT->value);
        $this->seedOrder('order-3', 'customer-123', OrderStatus::CANCELLED->value);

        Livewire::actingAs($this->authUser('staff-123'))
            ->test(OrderQueue::class)
            ->assertSee('Customer User')
            ->assertDontSee('order-3')
            ->call('filterBy', 'PAID_PROCESSING')
            ->assertSee('Ready to Ship')
            ->assertSee('Cancelled')
            ->assertSee('Unavailable')
            ->call('toggleDetails', 'order-1')
            ->assertSee('Chocolate Cake')
            ->call('updateStatus', 'order-1', OrderStatus::PICKUP_REQUESTED->value)
            ->assertSet('expandedOrderId', null);

        $this->assertSame(OrderStatus::PICKUP_REQUESTED->value, DB::table('orders')->where('id', 'order-1')->value('order_status'));
    }

    public function test_order_queue_paginates_ten_orders_per_page(): void
    {
        for ($index = 1; $index <= 11; $index++) {
            $this->seedOrder(
                sprintf('order-%02d', $index),
                'customer-123',
                OrderStatus::PAID_PROCESSING->value,
                now()->subMinutes($index)
            );
        }

        Livewire::actingAs($this->authUser('staff-123'))
            ->test(OrderQueue::class)
            ->assertSet('totalOrders', 11)
            ->assertCount('orders', 10)
            ->assertSee('ORDER-01')
            ->assertDontSee('ORDER-11')
            ->call('nextPage')
            ->assertSet('page', 2)
            ->assertCount('orders', 1)
            ->assertSee('ORDER-11');
    }

    public function test_order_queue_can_switch_to_five_orders_per_page(): void
    {
        for ($index = 1; $index <= 11; $index++) {
            $this->seedOrder(
                sprintf('order-%02d', $index),
                'customer-123',
                OrderStatus::PAID_PROCESSING->value,
                now()->subMinutes($index)
            );
        }

        Livewire::actingAs($this->authUser('staff-123'))
            ->test(OrderQueue::class)
            ->call('setPerPage', 5)
            ->assertSet('perPage', 5)
            ->assertSet('page', 1)
            ->assertCount('orders', 5)
            ->assertDontSee('ORDER-06');
    }

    public function test_status_update_rejects_invalid_transition(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PENDING_PAYMENT->value);

        Livewire::actingAs($this->authUser('staff-123'))
            ->test(OrderQueue::class)
            ->call('updateStatus', 'order-1', OrderStatus::ON_DELIVERY->value);

        $this->assertSame(OrderStatus::PENDING_PAYMENT->value, DB::table('orders')->where('id', 'order-1')->value('order_status'));
    }

    public function test_status_update_supports_scrum_50_legacy_processing_transition(): void
    {
        $this->seedOrder('order-1', 'customer-123', 'IN_PROCESSING');

        Livewire::actingAs($this->authUser('staff-123'))
            ->test(OrderQueue::class)
            ->call('updateStatus', 'order-1', OrderStatus::PICKUP_REQUESTED->value);

        $this->assertSame(OrderStatus::PICKUP_REQUESTED->value, DB::table('orders')->where('id', 'order-1')->value('order_status'));
    }

    public function test_status_update_blocks_processing_cancel_because_only_paid_or_pending_can_cancel(): void
    {
        $this->seedOrder('order-1', 'customer-123', 'IN_PROCESSING');

        Livewire::actingAs($this->authUser('staff-123'))
            ->test(OrderQueue::class)
            ->call('updateStatus', 'order-1', OrderStatus::CANCELLED->value);

        $this->assertSame('IN_PROCESSING', DB::table('orders')->where('id', 'order-1')->value('order_status'));
    }

    public function test_on_delivery_driver_failed_cancels_order_and_adds_note(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::ON_DELIVERY->value);

        Livewire::actingAs($this->authUser('staff-123'))
            ->test(OrderQueue::class)
            ->call('updateStatus', 'order-1', OrderStatus::CANCELLED->value);

        $order = DB::table('orders')->where('id', 'order-1')->first();

        $this->assertSame(OrderStatus::CANCELLED->value, $order->order_status);
        $this->assertStringContainsString('NOTED: Driver gagal kirim', $order->notes);
    }

    public function test_status_update_requires_owner_or_staff_role(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PAID_PROCESSING->value);

        $this->actingAs($this->authUser('customer-123'));
        $this->expectException(AuthorizationException::class);

        app(UpdateOrderStatusAction::class)('order-1', OrderStatus::PICKUP_REQUESTED->value);
    }

    public function test_manual_shipment_saves_tracking_without_biteship_and_completes_order(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PICKUP_REQUESTED->value);
        $this->seedOrderAddress('order-1');

        Livewire::actingAs($this->authUser('staff-123'))
            ->test(OrderQueue::class)
            ->call('openManualShipmentModal', 'order-1')
            ->assertSet('showManualShipmentModal', true)
            ->set('manualTrackingId', ' MANUAL-123 ')
            ->call('submitManualShipment')
            ->assertSet('showManualShipmentModal', false);

        $order = DB::table('orders')->where('id', 'order-1')->first();

        $this->assertSame(OrderStatus::COMPLETED->value, $order->order_status);
        $this->assertSame('MANUAL-123', $order->tracking_id);
        $this->assertNull($order->biteship_order_id);
    }

    public function test_manual_shipment_requires_tracking_number(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PICKUP_REQUESTED->value);
        $this->seedOrderAddress('order-1');

        Livewire::actingAs($this->authUser('staff-123'))
            ->test(OrderQueue::class)
            ->call('openManualShipmentModal', 'order-1')
            ->set('manualTrackingId', '   ')
            ->call('submitManualShipment')
            ->assertHasErrors(['manualTrackingId']);

        $order = DB::table('orders')->where('id', 'order-1')->first();

        $this->assertSame(OrderStatus::PICKUP_REQUESTED->value, $order->order_status);
        $this->assertNull($order->tracking_id);
    }

    public function test_manual_shipment_is_idempotent_when_same_tracking_already_saved(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::ON_DELIVERY->value);
        DB::table('orders')->where('id', 'order-1')->update(['tracking_id' => 'MANUAL-123']);

        $this->actingAs($this->authUser('staff-123'));

        $result = app(ManualShipmentAction::class)('order-1', ' MANUAL-123 ');
        $order = DB::table('orders')->where('id', 'order-1')->first();

        $this->assertTrue($result['success']);
        $this->assertTrue($result['idempotent']);
        $this->assertSame(OrderStatus::COMPLETED->value, $order->order_status);
        $this->assertSame('MANUAL-123', $order->tracking_id);
        $this->assertNull($order->biteship_order_id);
    }

    public function test_manual_shipment_rejects_order_that_already_has_biteship_pickup(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PICKUP_REQUESTED->value);
        DB::table('orders')->where('id', 'order-1')->update(['biteship_order_id' => 'BITESHIP-123']);

        $this->actingAs($this->authUser('staff-123'));

        $result = app(ManualShipmentAction::class)('order-1', 'MANUAL-123');
        $order = DB::table('orders')->where('id', 'order-1')->first();

        $this->assertFalse($result['success']);
        $this->assertSame('This order already has a Biteship pickup.', $result['message']);
        $this->assertSame(OrderStatus::PICKUP_REQUESTED->value, $order->order_status);
        $this->assertNull($order->tracking_id);
    }

    public function test_manual_shipment_requires_owner_or_staff_role(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PICKUP_REQUESTED->value);

        $this->actingAs($this->authUser('customer-123'));
        $this->expectException(AuthorizationException::class);

        app(ManualShipmentAction::class)('order-1', 'MANUAL-123');
    }

    private function seedOrder(string $orderId, string $customerId, string $status, $orderDate = null): void
    {
        $orderDate ??= now();

        DB::table('orders')->insert([
            'id' => $orderId,
            'customer_id' => $customerId,
            'order_date' => $orderDate,
            'total_payment' => 125000,
            'order_status' => $status,
            'biteship_order_id' => null,
            'tracking_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('order_items')->insert([
            'id' => 'item-' . $orderId,
            'order_id' => $orderId,
            'cake_name_snapshot' => 'Chocolate Cake',
            'quantity' => 2,
            'subtotal' => 100000,
        ]);
    }

    private function seedOrderAddress(string $orderId): void
    {
        DB::table('order_addresses')->insert([
            'id' => 'address-' . $orderId,
            'order_id' => $orderId,
            'recipient_name' => 'Customer User',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Testing No. 1',
            'city' => 'Jakarta',
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
