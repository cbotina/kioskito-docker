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
        // Schema::create('orders', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('payment_path');
        //     $table->unsignedBigInteger('user_id');
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->timestamps();
        // });

        // Schema::create('order_product', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('quantity');
        //     $table->unsignedBigInteger('order_id');
        //     $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        //     $table->unsignedBigInteger('product_id');
        //     $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::disableForeignKeyConstraints();
        // Schema::dropIfExists('order_product');
        // Schema::dropIfExists('orders');
        // Schema::enableForeignKeyConstraints();
    }
};
