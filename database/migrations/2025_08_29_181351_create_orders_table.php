<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/2025_08_29_181351_create_orders_table.php

public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();

        // [UBAH] Gunakan bigInteger dan referensi ke 'id'
        $table->unsignedBigInteger('user_id'); 
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        $table->decimal('total_price', 12, 0);
        $table->string('status')->default('pending');
        $table->string('customer_name');
        $table->string('customer_email');
        $table->text('shipping_address');
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