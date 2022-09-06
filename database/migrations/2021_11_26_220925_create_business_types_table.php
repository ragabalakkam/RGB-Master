<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTypesTable extends Migration
{
    public function up()
    {
        Schema::create('business_types', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('description')->nullable();
            $table->text('cashier_settings')->nullable();
            $table->text('modules')->nullable();
            $table->text('sales_systems')->nullable();
            $table->text('translations')->nullable();
            $table->text('database')->nullable();
            $table->text('forms')->default(json_encode([]));
            $table->text('zip_path')->nullable();

            # app
            $table->unsignedBigInteger('app_id');
            $table->foreign('app_id')->references('id')->on('apps')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('business_types');
    }
}
