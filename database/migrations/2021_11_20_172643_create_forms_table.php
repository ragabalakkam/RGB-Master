<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('icon')->nullable();
            $table->integer('order')->default(1);
            $table->string('width')->default('500px');
            $table->integer('cols')->default(12);
            $table->enum('dir', ['auto', 'rtl', 'ltr'])->default('ltr');
            $table->string('class')->nullable();
            $table->string('style')->nullable();
            $table->boolean('active')->default(false);
            $table->boolean('show_in_sales')->default(true);
            $table->boolean('show_in_purchases')->default(false);
            $table->boolean('show_in_forms')->default(false);
            $table->softDeletes();
        });
        
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->text('name');
            $table->string('icon')->nullable();
            $table->enum('datatype', ['text', 'number', 'password', 'boolean', 'array', 'date', 'datetime-local', 'email'])->default('text');
            $table->integer('order')->default(1);
            $table->integer('cols')->default(12);
            $table->string('class')->nullable();
            $table->string('style')->nullable();
            $table->enum('dir', ['auto', 'rtl', 'ltr'])->default('ltr');
            $table->boolean('required')->default(false);
            $table->boolean('active')->default(false);
            $table->softDeletes();

            # relationships
            $table->foreign('form_id')->references('id')->on('forms')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fields');
        Schema::dropIfExists('forms');
    }
}
