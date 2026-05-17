<?php

namespace Tests\Feature;

use App\Livewire\Admin\FinancialReportPage;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class AdminFinancialReportPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('role')->default('customer');
        });

        DB::table('users')->insert([
            ['id' => 'owner-123', 'role' => 'owner'],
            ['id' => 'customer-123', 'role' => 'customer'],
        ]);
    }

    public function test_owner_can_view_financial_report_ui(): void
    {
        $this->actingAs($this->authUser('owner-123'))
            ->get('/admin/reports')
            ->assertOk()
            ->assertSee('Monthly Sales Report')
            ->assertSee('Export PDF')
            ->assertSee('Export Excel')
            ->assertSee('Date')
            ->assertSee('Order No.')
            ->assertSee('Product')
            ->assertSee('Subtotal')
            ->assertSee('Shipping')
            ->assertSee('Grand Total')
            ->assertSee('Orders This Period')
            ->assertSee('No Transactions Yet');
    }

    public function test_non_owner_cannot_view_financial_report_route(): void
    {
        $this->actingAs($this->authUser('customer-123'))
            ->get('/admin/reports')
            ->assertRedirect('/');
    }

    public function test_financial_report_filters_are_livewire_state_only(): void
    {
        Livewire::actingAs($this->authUser('owner-123'))
            ->test(FinancialReportPage::class)
            ->set('month', 1)
            ->set('year', 2026)
            ->assertSet('month', 1)
            ->assertSet('year', 2026)
            ->assertSet('perPage', 20)
            ->assertSee('Grand Total');
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
