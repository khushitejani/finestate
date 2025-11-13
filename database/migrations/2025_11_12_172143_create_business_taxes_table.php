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
        Schema::create('business_taxis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('class')->nullable(); 
            $table->string('resource')->nullable();
            $table->decimal('income_per_hour', 15, 2)->default(0); 
            $table->decimal('price', 15, 2)->default(0); 
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_taxes');
    }
};
