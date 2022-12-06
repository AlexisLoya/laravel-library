<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BookReviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('book_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('comment')->nullable();
            $table->boolean('edited')->nullable()->default(0);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('book_id');
            // Create Foreign Keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('book_id')->references('id')->on('books');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
