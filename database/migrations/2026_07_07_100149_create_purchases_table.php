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
        Schema::create('purchases', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Purchase Information
            |--------------------------------------------------------------------------
            */

            $table->string('purchase_no')->unique();

            $table->date('purchase_date');

            $table->foreignId('supplier_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('invoice_no')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Amount
            |--------------------------------------------------------------------------
            */

            $table->decimal('subtotal', 12, 2)->default(0);

            $table->decimal('discount_percent', 5, 2)->default(0);

            $table->decimal('discount_amount', 12, 2)->default(0);

            $table->decimal('tax_percent', 5, 2)->default(0);

            $table->decimal('tax_amount', 12, 2)->default(0);

            $table->decimal('grand_total', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */

            $table->decimal('paid_amount', 12, 2)->default(0);

            $table->decimal('balance', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Others
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'Draft',
                'Pending',
                'Completed',
                'Cancelled'
            ])->default('Draft');

            $table->text('remark')->nullable();

            $table->foreignId('created_by')
                ->constrained('users');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};