<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('bio')->nullable();
            $table->string('phone_number')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('postal-code')->nullable();
            $table->text('address-line-1')->nullable();
            $table->text('address-line-2')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->text('education')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
