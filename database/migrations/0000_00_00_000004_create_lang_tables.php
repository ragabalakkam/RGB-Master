<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLangTables extends Migration
{
    public function up()
    {
        Schema::create('locales', function (Blueprint $table) {
            $table->id();
            $table->text('name', 255);
            $table->string('label');
            $table->enum('dir', ['ltr', 'rtl'])->default('ltr');
        });
        
        // Schema::create('translation_groups', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name')->default('untitled');
        //     $table->string('icon')->nullable();
        //     $table->string('description')->nullable();
        //     $table->integer('order')->nullable();
        // });
        
        Schema::create('translations', function (Blueprint $table) {
            $table->unsignedBigInteger('locale_id');
            $table->string('description')->nullable();
            $table->string('key');
            $table->text('value')->nullable();
            $table->unsignedBigInteger('translation_group_id')->nullable();
            
            # relationships
            $table->foreign('locale_id')->references('id')->on('locales')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('translation_group_id')->references('id')->on('translation_groups')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('translations');
        // Schema::dropIfExists('translation_groups');
        Schema::dropIfExists('locales');
    }
}
