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
        Schema::create('purchase_details', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            $table->foreignId('purchase_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Purchase Information
            |--------------------------------------------------------------------------
            */

            $table->decimal('qty', 12, 2)->default(0);

            // Actual unit cost paid on this purchase
            $table->decimal('unit_cost', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Item Discount
            |--------------------------------------------------------------------------
            */

            $table->decimal('discount_percent', 5, 2)->default(0);

            $table->decimal('discount_amount', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Total
            |--------------------------------------------------------------------------
            */

            $table->decimal('subtotal', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Optional Remark
            |--------------------------------------------------------------------------
            */

            $table->string('remark')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('purchase_id');
            $table->index('product_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};