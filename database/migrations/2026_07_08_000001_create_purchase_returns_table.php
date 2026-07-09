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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();

            $table->string('return_no')->unique();

            $table->date('return_date');

            $table->foreignId('purchase_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('supplier_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('subtotal', 12, 2)->default(0);

            $table->enum('status', [
                'Completed',
                'Cancelled',
            ])->default('Completed');

            $table->text('remark')->nullable();

            $table->foreignId('created_by')
                ->constrained('users');

            $table->timestamps();

            $table->index('return_no');
            $table->index('purchase_id');
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
