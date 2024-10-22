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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('subtotal')->default(0);
            $table->integer('delivery_fee')->default(0);
            $table->integer('additional_fee')->default(0);
            $table->integer('total')->default(0);
            $table->string('thumbnail')->nullable();
            $table->string('payment_url')->nullable();
            $table->string('receipt_code')->nullable();
            $table->enum('status', ['ON_CART', 'PENDING', 'ON_DELIVERY', 'SUCCESS', 'CANCELED'])->default('PENDING');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
