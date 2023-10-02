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
            Schema::create('stock', function (Blueprint $table) {
            $table->string('sku')->unique();
            $table->string('item_name');
            $table->string('price')->nullable();
            $table->string('quantity')->nullable();
            $table->string('business_id');
            $table->timestamp('date_updated')->nullable();
            $table->string('catalog_object_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};
