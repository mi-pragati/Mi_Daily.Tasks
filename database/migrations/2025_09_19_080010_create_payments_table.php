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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('payment_id')->nullable();      // Stripe PaymentIntent ID
        $table->string('method')->nullable();          // card, upi, wallet, qr
        $table->decimal('amount', 10, 2);              // amount paid
        $table->string('currency', 10)->default('INR');
        $table->string('status')->default('pending');  // succeeded, failed, pending
        $table->json('details')->nullable();           // store raw response if needed
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
