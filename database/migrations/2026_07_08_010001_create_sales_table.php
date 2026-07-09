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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('sale_no')->unique();

            $table->date('sale_date');

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('invoice_no')->nullable();

            $table->decimal('subtotal', 12, 2)->default(0);

            $table->decimal('discount_percent', 5, 2)->default(0);

            $table->decimal('discount_amount', 12, 2)->default(0);

            $table->decimal('tax_percent', 5, 2)->default(0);

            $table->decimal('tax_amount', 12, 2)->default(0);

            $table->decimal('grand_total', 12, 2)->default(0);

            $table->decimal('paid_amount', 12, 2)->default(0);

            $table->decimal('balance', 12, 2)->default(0);

            $table->decimal('change_amount', 12, 2)->default(0);

            $table->enum('status', [
                'Draft',
                'Pending',
                'Completed',
                'Cancelled',
            ])->default('Draft');

            $table->text('remark')->nullable();

            $table->foreignId('created_by')
                ->constrained('users');

            $table->timestamps();

            $table->index('sale_no');
            $table->index('sale_date');
            $table->index('customer_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
