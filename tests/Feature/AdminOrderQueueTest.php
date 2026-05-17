<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
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
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('cake_name_snapshot');
            $table->integer('quantity');
            $table->integer('subtotal');
        });

        DB::table('users')->insert([
            ['id' => 'admin-123', 'full_name' => 'Admin User', 'email' => 'admin@example.test', 'role' => 'admin'],
            ['id' => 'employee-123', 'full_name' => 'Employee User', 'email' => 'employee@example.test', 'role' => 'karyawan'],
            ['id' => 'customer-123', 'full_name' => 'Customer User', 'email' => 'customer@example.test', 'role' => 'customer'],
        ]);
    }

    public function test_admin_order_queue_route_renders_active_orders(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PAID_PROCESSING->value);

        $this->actingAs($this->authUser('admin-123'))
            ->get('/admin/orders')
            ->assertOk()
            ->assertSee('Order Queue')
            ->assertSee('Customer User')
            ->assertSee('Paid Processing')
            ->assertSee('Actions')
            ->assertSee('Available')
            ->assertSee('Unavailable')
            ->assertSee('Siap Dikirim')
            ->assertSee('Request Pickup');
    }

    public function test_customer_access_to_admin_order_queue_redirects_home(): void
    {
        $this->actingAs($this->authUser('customer-123'))
            ->get('/admin/orders')
            ->assertRedirect('/');
    }

    public function test_order_queue_filters_expands_and_updates_status(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PAID_PROCESSING->value);
        $this->seedOrder('order-2', 'customer-123', OrderStatus::PENDING_PAYMENT->value);
        $this->seedOrder('order-3', 'customer-123', OrderStatus::CANCELLED->value);

        Livewire::actingAs($this->authUser('admin-123'))
            ->test(OrderQueue::class)
            ->assertSee('Customer User')
            ->assertDontSee('order-3')
            ->call('filterBy', 'PAID_PROCESSING')
            ->assertSee('Siap Dikirim')
            ->assertSee('Batalkan')
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

        Livewire::actingAs($this->authUser('admin-123'))
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

        Livewire::actingAs($this->authUser('admin-123'))
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

        Livewire::actingAs($this->authUser('admin-123'))
            ->test(OrderQueue::class)
            ->call('updateStatus', 'order-1', OrderStatus::ON_DELIVERY->value);

        $this->assertSame(OrderStatus::PENDING_PAYMENT->value, DB::table('orders')->where('id', 'order-1')->value('order_status'));
    }

    public function test_status_update_requires_admin_or_employee_role(): void
    {
        $this->seedOrder('order-1', 'customer-123', OrderStatus::PAID_PROCESSING->value);

        $this->actingAs($this->authUser('customer-123'));
        $this->expectException(AuthorizationException::class);

        app(UpdateOrderStatusAction::class)('order-1', OrderStatus::PICKUP_REQUESTED->value);
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

    private function authUser(string $id): User
    {
        $user = new User();
        $user->id = $id;
        $user->role = DB::table('users')->where('id', $id)->value('role');
        $user->exists = true;

        return $user;
    }
}
