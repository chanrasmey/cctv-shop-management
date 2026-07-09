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
        Schema::table('products', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Inventory Cost Information
            |--------------------------------------------------------------------------
            */

            // Weighted Average Cost
            $table->decimal('average_cost', 12, 2)
                ->default(0)
                ->after('sell_price');

            // Latest purchase price from supplier
            $table->decimal('last_purchase_price', 12, 2)
                ->default(0)
                ->after('average_cost');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropColumn([
                'average_cost',
                'last_purchase_price',
            ]);

        });
    }
};