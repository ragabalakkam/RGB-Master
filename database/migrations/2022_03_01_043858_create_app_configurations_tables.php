<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppConfigurationsTables extends Migration
{
    public function up()
    {
        Schema::create('app_configurations', function (Blueprint $table) {
            $table->id();

            # app
            $table->unsignedBigInteger('app_id');
            $table->foreign('app_id')->references('id')->on('apps')->onUpdate('cascade')->onDelete('cascade');

            $table->text('name');
            $table->text('description')->nullable();
            $table->string('key')->unique()->index();
            $table->enum('datatype', ['text', 'number', 'password', 'boolean', 'array', 'date', 'datetime-local', 'email'])->default('text');
            $table->text('default')->nullable();

            # flags
            $table->boolean('required')->default(false);
            $table->boolean('hidden')->default(false);
            $table->boolean('disabled')->default(false);
        });

        Schema::create('app_configuration_groups', function (Blueprint $table) {
            $table->id();

            # app
            $table->unsignedBigInteger('app_id');
            $table->foreign('app_id')->references('id')->on('apps')->onUpdate('cascade')->onDelete('cascade');
            
            $table->text('name');
            $table->text('description')->nullable();
            $table->text('key');

            # flags
            $table->boolean('hidden')->default(false);
            $table->boolean('disabled')->default(false);
        });

        Schema::create('app_configuration_group', function (Blueprint $table) {
            $table->id();
            
            # group
            $table->unsignedBigInteger('app_configuration_group_id');
            $table->foreign('app_configuration_group_id')->references('id')->on('app_configuration_groups')->onUpdate('cascade')->onDelete('cascade');

            # configuration
            $table->unsignedBigInteger('app_configuration_id');
            $table->foreign('app_configuration_id')->references('id')->on('app_configurations')->onUpdate('cascade')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('app_configuration_group');
        Schema::dropIfExists('app_configuration_groups');
        Schema::dropIfExists('app_configurations');
    }
}
