<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            return; // Table already exists in Supabase
        }

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('cake_name');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->integer('weight_grams')->nullable();
            $table->text('image_url')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestampTz('created_at')->nullable()->default(DB::raw('now()'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
