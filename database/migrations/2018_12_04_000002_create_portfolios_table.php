<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->increments('id');
            $table->String('company');
            $table->String('ticker');
            $table->double('amount');
            $table->double('current_price');
            $table->String("native_currency");
            $table->double("total");
            $table->String('email');
            $table->timestamp('purchase_date');
            $table->double('change');
            $table->double('close_yesterday');
            $table->foreign('email')->references('email')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portfolios');
    }
}