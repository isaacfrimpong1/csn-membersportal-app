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
        Schema::create('Sales', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->string('order_id');
            $table->string('order_date');
            $table->string('item_name');
            $table->string('base_price');
            $table->string('discount')->nullable();
            $table->string('gross_amount');
            $table->string('quantity');
            $table->string('catalog_object_id')->nullable();
            $table->string('total_money')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Sales');
    }
};
