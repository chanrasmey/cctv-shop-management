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
        Schema::create('company_settings', function (Blueprint $table) {

            $table->id();

            $table->string('company_name',150);

            $table->string('company_name_kh',150)->nullable();

            $table->string('owner_name',150)->nullable();

            $table->string('logo')->nullable();

            $table->string('favicon')->nullable();

            $table->text('address')->nullable();

            $table->string('phone',50)->nullable();

            $table->string('mobile',50)->nullable();

            $table->string('email')->nullable();

            $table->string('website')->nullable();

            $table->string('telegram')->nullable();

            $table->string('facebook')->nullable();

            $table->string('tax_number')->nullable();

            $table->string('business_registration')->nullable();

            $table->string('invoice_prefix',20)->default('INV');

            $table->string('purchase_prefix',20)->default('PUR');

            $table->string('quotation_prefix',20)->default('QT');

            $table->string('currency',20)->default('USD');

            $table->string('currency_symbol',10)->default('$');

            $table->string('timezone')->default('Asia/Phnom_Penh');

            $table->text('invoice_footer')->nullable();

            $table->text('warranty_policy')->nullable();

            $table->text('receipt_note')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};