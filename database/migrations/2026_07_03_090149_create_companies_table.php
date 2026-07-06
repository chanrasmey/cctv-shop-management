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
       Schema::create('companies', function (Blueprint $table) {
    $table->id();

    $table->string('company_name');
    $table->string('owner_name')->nullable();

    $table->string('phone')->nullable();
    $table->string('email')->nullable();

    $table->string('website')->nullable();

    $table->text('address')->nullable();

    $table->string('logo')->nullable();

    $table->string('tax_number')->nullable();

    $table->string('currency')->default('USD');

    $table->string('timezone')->default('Asia/Phnom_Penh');

    $table->string('invoice_prefix')->default('INV');

    $table->text('invoice_footer')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
