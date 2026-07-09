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
        Schema::create('stock_movements', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Movement
            |--------------------------------------------------------------------------
            */

            $table->enum('movement_type', [
                'Purchase',
                'Sale',
                'Purchase Return',
                'Sales Return',
                'Adjustment',
                'Opening Stock',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Reference
            |--------------------------------------------------------------------------
            */

            $table->string('reference_no')->nullable();

            $table->unsignedBigInteger('reference_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            $table->decimal('qty_in', 12, 2)->default(0);

            $table->decimal('qty_out', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Running Balance
            |--------------------------------------------------------------------------
            */

            $table->decimal('balance', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Cost Information
            |--------------------------------------------------------------------------
            */

            $table->decimal('unit_cost', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Remark
            |--------------------------------------------------------------------------
            */

            $table->string('remark')->nullable();

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->constrained('users');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('product_id');

            $table->index('movement_type');

            $table->index('reference_no');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};