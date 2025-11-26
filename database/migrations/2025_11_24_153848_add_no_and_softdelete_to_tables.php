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
        $tables = [
            'aircraft_shops',
            'business_shippings',
            'business_taxis',
            'cards',
            'carshowrooms',
            'coins',
            'cryptocurrencies',
            'improvements',
            'insights',
            'islands',
            'jewelleds',
            'nfts',
            'paintings',
            'properties',
            'retro_cars',
            'shares',
            'stamps',
            'unique_items',
            'yatch_shops'
        ];

        foreach ($tables as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'no')) {
                    $table->integer('no')->nullable()->after('id'); 
                }
                if (!Schema::hasColumn($table->getTable(), 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'aircraft_shops',
            'business_shippings',
            'business_taxis',
            'cards',
            'carshowrooms',
            'coins',
            'cryptocurrencies',
            'improvements',
            'insights',
            'islands',
            'jewelleds',
            'nfts',
            'paintings',
            'properties',
            'retro_cars',
            'shares',
            'stamps',
            'unique_items',
            'yatch_shops'
        ];

        foreach ($tables as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'no')) {
                    $table->dropColumn('no');
                }
                if (Schema::hasColumn($table->getTable(), 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }
    }
};
