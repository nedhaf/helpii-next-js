<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('badge_id')->nullable();
            $table->string('phone', 15)->nullable();
            $table->integer('experience')->nullable();
            $table->text('about')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('pincode', 50)->nullable();
            $table->string('longitudes', 50)->nullable();
            $table->string('latitude', 50)->nullable();
            $table->string('banner_image', 300)->nullable();
            $table->string('metadescription', 200)->nullable();
            $table->string('metatag', 255)->nullable();
            $table->string('facebook', 500)->nullable();
            $table->string('twitter', 500)->nullable();
            $table->string('linkedin', 500)->nullable();
            $table->string('instagram', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('badge_id')->references('id')->on('user_badges')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile');
    }
}
