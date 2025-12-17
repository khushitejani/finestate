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
        Schema::create('constructions', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->string('name');
            $table->string('metal')->nullable();
            $table->string('builder_men')->nullable();
            $table->string('wood')->nullable();
            $table->string('concrete')->nullable();
            $table->decimal('total_cost_of_construction', 15, 2)->default(0);
            $table->decimal('total_profit', 15, 2)->default(0);
            $table->decimal('total_percentage', 15, 2)->default(0);
            $table->string('time')->nullable();
            $table->string('image')->nullable();
            $table->decimal('total_return_after_completion', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constructions');
    }
};
