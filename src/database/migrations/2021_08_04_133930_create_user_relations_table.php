<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRelationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('relatingUserID');
            $table->foreign('relatingUserID')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('relatedUserID');
            $table->foreign('relatedUserID')->references('id')->on('users')->cascadeOnDelete();
            $table->enum('relation', ['request', 'block', 'friend', 'rejected']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('user_relations');
    }
}
