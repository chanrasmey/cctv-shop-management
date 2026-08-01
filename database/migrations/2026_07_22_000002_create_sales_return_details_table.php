<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_return_details', function (Blueprint $table) {

            $table->id();

            $table->foreignId('sales_return_id')
                ->constrained('sales_returns')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('sale_detail_id')
                ->constrained('sale_details')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('qty', 18, 2);

            $table->decimal('unit_price', 18, 2);

            $table->decimal('unit_cost', 18, 2);

            $table->decimal('subtotal', 18, 2);

            $table->decimal('profit_adjustment', 18, 2)
                ->default(0);

            $table->string('reason')
                ->nullable();

            $table->text('remark')
                ->nullable();

            $table->timestamps();

            $table->index('sales_return_id');
            $table->index('sale_detail_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_details');
    }
};