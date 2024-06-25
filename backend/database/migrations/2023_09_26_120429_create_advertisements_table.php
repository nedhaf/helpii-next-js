<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('skill_id')->nullable();
            $table->string('title', 300);
            $table->string('phone', 300)->nullable();
            $table->string('link', 300)->nullable();
            $table->string('image', 300)->nullable();
            $table->text('description')->nullable();
            $table->string('color')->default('#ffffff');
            $table->string('badge_img')->nullable();
            $table->string('city')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('position')->default('top');
            $table->decimal('cost', 8,2)->nullable();
            $table->tinyInteger('isFront')->default(0)->unsigned();
            $table->tinyInteger('show_in_front_profile')->default(0)->unsigned();
            $table->tinyInteger('show_in_front_ads')->default(0)->unsigned();
            $table->tinyInteger('status')->default(1)->unsigned();
            $table->timestamps();

            $table->foreign('user_id', 'fk_advertisements_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('skill_id', 'fk_advertisements_skill_id')->references('id')->on('skills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropForeign('user_id');
        Schema::dropForeign('skill_id');
        Schema::dropIfExists('advertisements');
    }
}
