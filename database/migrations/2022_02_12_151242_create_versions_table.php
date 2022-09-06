<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersionsTable extends Migration
{
    public function up()
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->id();

            # version type & number
            $table->enum('type', ['major', 'minor', 'patch']);
            $table->integer('major')->default(0);
            $table->integer('minor')->default(0);
            $table->integer('patch')->default(0);
            
            # app
            $table->unsignedBigInteger('app_id');
            $table->foreign('app_id')->references('id')->on('apps')->onUpdate('cascade')->onDelete('cascade');
            
            # uploaded by
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            
            $table->string('path');
            
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            # flags
            $table->boolean('stable')->default(false);

            # timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('versions');
    }
}
