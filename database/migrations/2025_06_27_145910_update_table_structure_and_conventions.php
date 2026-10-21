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
        // =================================================================
        // PERUBAHAN PADA FOREIGN KEY (HARUS DILAKUKAN PERTAMA)
        // =================================================================
        Schema::table('stock_movements', function (Blueprint $table) {
            // Hapus foreign key constraint sementara sebelum me-rename kolom id di tabel products
            $table->dropForeign(['product_id']);
        });

        // =================================================================
        // PERUBAHAN TABEL `users`
        // =================================================================
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id', 'id_user');
            $table->string('name', 30)->change();
            $table->string('email', 30)->change();
            $table->string('password', 100)->change(); // Sesuai permintaan, password diubah ke 100
        });

        // =================================================================
        // PERUBAHAN TABEL `products`
        // =================================================================
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('id', 'id_product');
            $table->string('name', 30)->change();
        });

        // =================================================================
        // PERUBAHAN TABEL `stock_movements`
        // =================================================================
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->renameColumn('id', 'id_stock_movement');
            $table->string('payment_method', 30)->change();

            // Buat kembali foreign key yang merujuk ke kolom id_product yang baru di tabel products
            $table->foreign('product_id')->references('id_product')->on('products')->onDelete('cascade');
        });
        
        // =================================================================
        // PERUBAHAN TABEL `failed_jobs`
        // =================================================================
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->renameColumn('id', 'id_failed_job');
            // PERHATIAN: Kolom uuid TIDAK diubah ke 30 karakter karena UUID memiliki panjang standar 36 karakter.
            // Mengubahnya akan menyebabkan error. Jadi kita biarkan saja.
            // $table->string('uuid', 30)->change(); // <-- JANGAN LAKUKAN INI
        });

        // =================================================================
        // PERUBAHAN TABEL `password_reset_tokens`
        // =================================================================
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Tabel ini tidak memiliki kolom 'id', jadi hanya mengubah panjang token
            $table->string('token', 30)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Proses rollback harus dilakukan dalam urutan terbalik
        
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->string('token', 255)->change();
        });

        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->renameColumn('id_failed_job', 'id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->renameColumn('id_product', 'id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->renameColumn('id_stock_movement', 'id');
            $table->string('payment_method', 255)->change();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id_user', 'id');
            $table->string('name', 255)->change();
            $table->string('email', 255)->change();
            $table->string('password', 255)->change();
        });
    }
};