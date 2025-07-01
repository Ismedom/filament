<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePymedHeroSectionsTable extends Migration
{
    public function up()
    {
        Schema::create('pymed_hero_sections', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('image')->nullable();
            $table->string('bg_image')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pymed_hero_sections');
    }
}
