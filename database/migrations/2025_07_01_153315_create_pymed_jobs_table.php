<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePymedJobsTable extends Migration
{
    public function up()
    {
        Schema::create('pymed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('title');
            $table->string('image')->nullable();
            $table->text('sort_description')->nullable();
            $table->text('description')->nullable();
            $table->string('quote')->nullable();
            $table->json('datas')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pymed_jobs');
    }
}
