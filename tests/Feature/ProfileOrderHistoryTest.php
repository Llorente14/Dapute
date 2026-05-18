<?php

namespace Tests\Feature;

use App\Actions\Orders\FetchUserOrdersAction;
use App\Livewire\Profile\ProfileOrderHistory;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileOrderHistoryTest extends TestCase
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
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('customer_id');
            $table->timestamp('order_date')->nullable();
            $table->string('order_status');
            $table->integer('subtotal_amount')->default(0);
            $table->integer('shipping_fee')->default(0);
            $table->integer('total_payment')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('cake_name_snapshot');
            $table->integer('price_snapshot');
            $table->integer('quantity');
            $table->integer('subtotal');
        });

        DB::table('users')->insert([
            ['id' => 'customer-123', 'full_name' => 'Customer User', 'email' => 'customer@example.test'],
            ['id' => 'other-123', 'full_name' => 'Other User', 'email' => 'other@example.test'],
        ]);
    }

    public function test_fetch_user_orders_returns_nested_items_and_filters_by_status(): void
    {
        $this->seedOrder('order-1', 'customer-123', 'PENDING_PAYMENT', '2026-05-18 10:00:00');
        $this->seedOrder('order-2', 'customer-123', 'COMPLETED', '2026-05-17 10:00:00');
        $this->seedOrder('order-3', 'other-123', 'COMPLETED', '2026-05-16 10:00:00');

        $orders = app(FetchUserOrdersAction::class)->execute('customer-123', 'COMPLETED');

        $this->assertCount(1, $orders);
        $this->assertSame('order-2', $orders[0]['id']);
        $this->assertSame(100000, $orders[0]['total_payment']);
        $this->assertCount(1, $orders[0]['items']);
        $this->assertSame('Kue See', $orders[0]['items'][0]['cake_name_snapshot']);
    }

    public function test_profile_order_history_renders_and_filters_without_page_reload(): void
    {
        $this->seedOrder('order-1', 'customer-123', 'PENDING_PAYMENT', '2026-05-18 10:00:00');
        $this->seedOrder('order-2', 'customer-123', 'COMPLETED', '2026-05-17 10:00:00');

        Livewire::actingAs($this->authUser('customer-123'))
            ->test(ProfileOrderHistory::class)
            ->assertSee('Recent Orders')
            ->assertSee('Completed')
            ->assertSee('Kue See')
            ->call('setFilter', 'COMPLETED')
            ->assertSet('filterStatus', 'COMPLETED')
            ->assertCount('orders', 1)
            ->assertSee('Completed');
    }

    public function test_profile_order_history_empty_state(): void
    {
        Livewire::actingAs($this->authUser('customer-123'))
            ->test(ProfileOrderHistory::class)
            ->assertSee('No orders yet');
    }

    private function seedOrder(string $orderId, string $customerId, string $status, string $createdAt): void
    {
        DB::table('orders')->insert([
            'id' => $orderId,
            'customer_id' => $customerId,
            'order_date' => $createdAt,
            'order_status' => $status,
            'subtotal_amount' => 85000,
            'shipping_fee' => 15000,
            'total_payment' => 100000,
            'notes' => 'No peanuts',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        DB::table('order_items')->insert([
            'id' => 'item-' . $orderId,
            'order_id' => $orderId,
            'cake_name_snapshot' => 'Kue See',
            'price_snapshot' => 85000,
            'quantity' => 1,
            'subtotal' => 85000,
        ]);
    }

    private function authUser(string $id): User
    {
        $user = new User();
        $user->id = $id;
        $user->exists = true;

        return $user;
    }
}
