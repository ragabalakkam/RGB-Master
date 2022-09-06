<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTables extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->text('name');
        });
    
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->unsignedBigInteger('country_id');
            
            # relationships
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
        });
        
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->unsignedBigInteger('state_id');
            
            # relationships
            $table->foreign('state_id')->references('id')->on('states')->onUpdate('cascade')->onDelete('cascade');
        });
        
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->unsignedBigInteger('city_id');
            $table->text('neCoordinates')->nullable();
            $table->text('swCoordinates')->nullable();

            # relationships
            $table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('districts');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
    }
}
