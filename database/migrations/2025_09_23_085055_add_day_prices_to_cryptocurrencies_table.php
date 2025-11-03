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
        Schema::table('cryptocurrencies', function (Blueprint $table) {
            $table->json('day_prices')->nullable()->after('available_for_purchase');
        });
    }

    public function down(): void
    {
        Schema::table('cryptocurrencies', function (Blueprint $table) {
            $table->dropColumn(['day_prices']);
        });
    }
};
