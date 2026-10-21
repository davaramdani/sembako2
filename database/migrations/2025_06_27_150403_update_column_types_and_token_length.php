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
        // PERHATIAN: Migrasi ini berasumsi migrasi sebelumnya (UpdateTableStructureAndConventions) sudah dijalankan
        // dan nama kolom id sudah berubah menjadi id_nama_tabel.

        // Langkah 1: Hapus foreign key sementara untuk mengubah tipe data
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        // Langkah 2: Ubah kolom-kolom di tabel products dan users
        Schema::table('products', function (Blueprint $table) {
            // Mengubah primary key dari BIGINT menjadi INT
            $table->integer('id_product')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            // Mengubah primary key dari BIGINT menjadi INT
            $table->integer('id_user')->change();
            // Mengubah panjang remember_token dari 100 ke 30
            $table->string('remember_token', 30)->nullable()->change();
        });
        
        // Langkah 3: Ubah kolom-kolom di tabel yang bergantung (stock_movements)
        Schema::table('stock_movements', function (Blueprint $table) {
            // Mengubah primary key dari BIGINT menjadi INT
            $table->integer('id_stock_movement')->change();
            // Mengubah foreign key dari BIGINT menjadi INT agar cocok
            $table->integer('product_id')->change();
        });

        // Langkah 4: Ubah kolom di tabel sisanya
        Schema::table('failed_jobs', function (Blueprint $table) {
            // Mengubah primary key dari BIGINT menjadi INT
            $table->integer('id_failed_job')->change();
        });

        // Langkah 5: Buat kembali foreign key dengan tipe data yang sudah sesuai
        Schema::table('stock_movements', function (Blueprint $table) {
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
        // Proses rollback harus dilakukan dalam urutan terbalik

        // Langkah 1 (rollback): Hapus foreign key
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        // Langkah 2 (rollback): Kembalikan tipe data ke BIGINT
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->bigInteger('id_failed_job')->change();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->bigInteger('id_stock_movement')->change();
            $table->bigInteger('product_id')->change();
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('id_product')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('id_user')->change();
            // Kembalikan panjang remember_token ke 100
            $table->string('remember_token', 100)->nullable()->change();
        });

        // Langkah 3 (rollback): Buat kembali foreign key dengan tipe data BIGINT
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreign('product_id')->references('id_product')->on('products')->onDelete('cascade');
        });
    }
};