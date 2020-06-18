<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListrsosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listrso', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_rso');
            $table->foreign('id_rso')->references('id')->on('rso')->onDelete('cascade');
            $table->unsignedInteger('id_barang');
            $table->foreign('id_barang')->references('id')->on('barang')->onDelete('cascade');
            $table->integer('qty');
            $table->enum('status_stok',['Y','N'])->default('Y');
            $table->dateTime('tgl_datang');
            $table->enum('status',['Y','N'])->default('Y');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listrso');
    }
}
