<?php

namespace Tests\Feature;

use App\Actions\Reports\FetchMonthlyFinancialReportAction;
use App\Actions\Reports\ExportMonthlyFinancialReportAction;
use App\Enums\OrderStatus;
use App\Livewire\Admin\FinancialReportPage;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
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

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('role')->default('customer');
        });

        DB::table('users')->insert([
            ['id' => 'owner-123', 'role' => 'owner'],
            ['id' => 'staff-123', 'role' => 'staff'],
            ['id' => 'customer-123', 'role' => 'customer'],
        ]);

        Schema::create('orders', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->timestamp('order_date')->nullable();
            $table->integer('subtotal_amount')->default(0);
            $table->integer('shipping_fee')->default(0);
            $table->integer('total_payment')->default(0);
            $table->string('order_status');
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
            ->assertSee('Top Products Bought')
            ->assertSee('Revenue Split')
            ->assertSee('No Transactions Yet');
    }

    public function test_non_owner_cannot_view_financial_report_route(): void
    {
        $this->actingAs($this->authUser('customer-123'))
            ->get('/admin/reports')
            ->assertNotFound();

        $this->actingAs($this->authUser('staff-123'))
            ->get('/admin/reports')
            ->assertNotFound();
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

    public function test_monthly_financial_report_action_returns_aggregated_completed_orders_only(): void
    {
        $this->seedOrder('paid-1', '2026-05-08 10:00:00', OrderStatus::PAID_PROCESSING->value, 100000, 20000, 122500);
        $this->seedOrder('done-1', '2026-05-09 10:00:00', OrderStatus::COMPLETED->value, 50000, 15000, 67500);
        $this->seedOrder('pending-1', '2026-05-10 10:00:00', OrderStatus::PENDING_PAYMENT->value, 999999, 999999, 999999);
        $this->seedOrder('cancelled-1', '2026-05-11 10:00:00', OrderStatus::CANCELLED->value, 999999, 999999, 999999);
        $this->seedOrder('outside-month', '2026-04-08 10:00:00', OrderStatus::COMPLETED->value, 999999, 999999, 999999);

        $report = app(FetchMonthlyFinancialReportAction::class)->execute(5, 2026);

        $this->assertCount(2, $report['rows']);
        $this->assertSame(150000, $report['summary']['product_revenue']);
        $this->assertSame(35000, $report['summary']['shipping_revenue']);
        $this->assertSame(190000, $report['summary']['grand_total']);
        $this->assertSame(2, $report['summary']['order_count']);
        $this->assertSame(['DONE-1', 'PAID-1'], array_column($report['rows'], 'order_no'));
        $this->assertSame('Kue done-1', $report['charts']['top_products'][0]['label']);
        $this->assertSame(81, $report['charts']['revenue_split'][0]['percentage']);
    }

    public function test_financial_report_livewire_loads_rows_from_month_and_year_filter(): void
    {
        $this->seedOrder('may-1', '2026-05-08 10:00:00', OrderStatus::COMPLETED->value, 100000, 20000, 122500);
        $this->seedOrder('january-1', '2026-01-08 10:00:00', OrderStatus::COMPLETED->value, 50000, 10000, 62500);

        Livewire::actingAs($this->authUser('owner-123'))
            ->test(FinancialReportPage::class)
            ->set('month', 1)
            ->set('year', 2026)
            ->assertSet('reportRows.0.order_no', 'JANUARY-');
    }

    public function test_financial_report_livewire_exposes_umkm_chart_data(): void
    {
        $this->seedOrder('brownies-1', '2026-05-08 10:00:00', OrderStatus::COMPLETED->value, 100000, 20000, 122500);
        $this->seedOrder('nastar-1', '2026-05-09 10:00:00', OrderStatus::COMPLETED->value, 75000, 10000, 87500);

        Livewire::actingAs($this->authUser('owner-123'))
            ->test(FinancialReportPage::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->assertSee('Top Products Bought')
            ->assertSee('Revenue Split')
            ->assertSet('reportCharts.top_products.0.quantity', 1)
            ->assertSet('reportCharts.revenue_split.0.label', 'Product Sales');
    }

    public function test_owner_can_download_pdf_and_excel_reports_with_expected_filename(): void
    {
        $this->actingAs($this->authUser('owner-123'));
        $this->seedOrder('may-1', '2026-05-08 10:00:00', OrderStatus::COMPLETED->value, 100000, 20000, 122500);

        $action = app(ExportMonthlyFinancialReportAction::class);

        $pdf = $action->pdf(5, 2026);
        $excel = $action->excel(5, 2026);

        $this->assertStringContainsString('laporan-dapute-2026-05.pdf', $pdf->headers->get('content-disposition'));
        $this->assertStringContainsString('laporan-dapute-2026-05.xlsx', $excel->headers->get('content-disposition'));
    }

    public function test_owner_can_download_pdf_and_excel_reports_from_http_routes(): void
    {
        $this->actingAs($this->authUser('owner-123'));
        $this->seedOrder('may-1', '2026-05-08 10:00:00', OrderStatus::COMPLETED->value, 100000, 20000, 122500);

        $this->get('/admin/reports/export/pdf?month=5&year=2026')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=laporan-dapute-2026-05.pdf');

        $this->get('/admin/reports/export/excel?month=5&year=2026')
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=laporan-dapute-2026-05.xlsx');
    }

    public function test_non_owner_cannot_export_financial_report_from_backend(): void
    {
        $this->actingAs($this->authUser('customer-123'));

        $this->expectException(AuthorizationException::class);

        app(ExportMonthlyFinancialReportAction::class)->pdf(5, 2026);
    }

    private function authUser(string $id): User
    {
        $user = new User();
        $user->id = $id;
        $user->role = DB::table('users')->where('id', $id)->value('role');
        $user->exists = true;

        return $user;
    }

    private function seedOrder(string $id, string $orderDate, string $status, int $subtotal, int $shipping, int $total): void
    {
        DB::table('orders')->insert([
            'id' => $id,
            'order_date' => $orderDate,
            'subtotal_amount' => $subtotal,
            'shipping_fee' => $shipping,
            'total_payment' => $total,
            'order_status' => $status,
            'created_at' => $orderDate,
            'updated_at' => $orderDate,
        ]);

        DB::table('order_items')->insert([
            'id' => 'item-' . $id,
            'order_id' => $id,
            'cake_name_snapshot' => 'Kue ' . $id,
            'quantity' => 1,
            'subtotal' => $subtotal,
        ]);
    }
}
