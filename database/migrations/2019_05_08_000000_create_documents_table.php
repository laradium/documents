<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{

    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->unsigned();
            $table->string('title');
            $table->longText('content');
            $table->string('type');
            $table->integer('revision')->default('1');
            $table->string('key')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('documents');
    }
}
