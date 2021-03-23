<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->text('description')->nullable(true);
            $table->string('thumnail');
            $table->string('url_link'); 
            $table->text('meta_keywords')->nullable(true);
            $table->text('meta_description')->nullable(true);
            $table->boolean('is_published');
            $table->datetime('published_at')->nullable(true);
            $table->integer('is_portofolio')->default(0);
            $table->integer('seen')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
