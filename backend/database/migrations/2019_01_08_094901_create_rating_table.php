<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rating', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_userid');
            $table->unsignedBigInteger('to_userid');
            $table->integer('value_for_money')->unsigned()->nullable();
            $table->integer('quality_of_work')->unsigned()->nullable();
            $table->integer('relation_with_customer')->unsigned()->nullable();
            $table->integer('performance')->unsigned()->nullable();
            $table->integer('total')->unsigned()->nullable();
            $table->text('review')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('from_userid')->references('id')->on('users');
            $table->foreign('to_userid')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rating');
    }
}
