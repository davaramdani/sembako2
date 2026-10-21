<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // PERHATIAN: Hapus foreign key sementara agar bisa mengubah primary key
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        // Ubah semua primary key untuk menambahkan kembali AUTO_INCREMENT
        Schema::table('users', function (Blueprint $table) {
            // Sintaks ini akan mengubah kolom menjadi integer, unsigned, dan auto-incrementing
            $table->increments('id_user')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->increments('id_product')->change();
        });
        
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->increments('id_stock_movement')->change();
        });
        
        Schema::table('failed_jobs', function (Blueprint $table) {
            // Untuk failed_jobs, id-nya adalah BIGINT, jadi kita gunakan bigIncrements
            $table->bigIncrements('id_failed_job')->change();
        });

        // Buat kembali foreign key
        Schema::table('stock_movements', function (Blueprint $table) {
            // Pastikan tipe data foreign key (product_id) adalah integer biasa (bukan auto-increment)
            // Namun, karena migrasi sebelumnya sudah mengubahnya jadi INT, kita hanya perlu membuat ulang relasinya.
            $table->unsignedInteger('product_id')->change(); // Pastikan unsigned agar cocok dengan `increments()`
            $table->foreign('product_id')->references('id_product')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Method down() sengaja dikosongkan untuk migrasi perbaikan seperti ini
        // karena mengembalikannya ke state yang "salah" tidak ada gunanya.
        // Jika Anda ingin membuatnya lengkap, Anda harus mengubahnya kembali menjadi INT non-increment.
    }
};