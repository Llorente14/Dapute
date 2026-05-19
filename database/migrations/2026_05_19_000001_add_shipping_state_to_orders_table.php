<?php

use App\Enums\ShippingType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (!Schema::hasColumn('orders', 'shipping_type')) {
                $table->string('shipping_type')->default(ShippingType::ONLINE_COURIER->value)->after('order_status');
            }

            if (!Schema::hasColumn('orders', 'shipping_status')) {
                $table->string('shipping_status')->nullable()->after('shipping_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (Schema::hasColumn('orders', 'shipping_status')) {
                $table->dropColumn('shipping_status');
            }

            if (Schema::hasColumn('orders', 'shipping_type')) {
                $table->dropColumn('shipping_type');
            }
        });
    }
};
