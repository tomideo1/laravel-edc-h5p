<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateH5pContentsUserDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h5p_contents_user_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('content_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('sub_content_id')->unsigned();
            $table->string('data_id', 127);
            $table->text('data');
            $table->boolean('preload')->default(0);
            $table->boolean('invalidate')->default(0);
            $table->timestamps();
            $table->index(['content_id', 'user_id', 'sub_content_id', 'data_id'], 'content_user_sub_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('h5p_contents_user_data');
    }
}
