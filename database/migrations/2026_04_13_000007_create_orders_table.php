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
		Schema::create('orders', function (Blueprint $table) {
			$table->id();
			$table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
			$table->decimal('total_price', 12, 2);
			$table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
			$table->string('shipping_name');
			$table->string('shipping_phone', 30);
			$table->text('shipping_address');
			$table->enum('payment_method', ['transfer', 'cod']);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('orders');
	}
};
