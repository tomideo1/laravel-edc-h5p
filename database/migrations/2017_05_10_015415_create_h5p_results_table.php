<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateH5pResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h5p_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('content_id')->unsigned();
            $table->string('subcontent_id', 50)->nullable();
            $table->uuid('user_id');
            $table->bigInteger('score');
            $table->bigInteger('max_score');
            $table->dateTime('opened');
            $table->dateTime('finished');
            $table->bigInteger('time');

            $table->text('description', 65535)->nullable();
            $table->text('correct_responses_pattern', 65535)->nullable();
            $table->text('response', 65535)->nullable();
            $table->text('additionals', 65535)->nullable();

            $table->index(['content_id', 'user_id'], 'content_user');
            $table->index(['content_id', 'subcontent_id', 'user_id'], 'content_subcontent_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('h5p_results');
    }
}
