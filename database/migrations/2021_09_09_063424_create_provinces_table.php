<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('vietnam-zone.tables.provinces'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string(config('vietnam-zone.columns.name'));
            $table->string(config('vietnam-zone.columns.slug'));
            $table->string(config('vietnam-zone.columns.gso_id'));
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
        Schema::dropIfExists(config('vietnam-zone.tables.provinces'));
    }
}
