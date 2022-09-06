<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->enum('datatype', ['string', 'integer', 'float', 'boolean', 'timestamp', 'array', 'email', 'date'])->default('string');
            $table->text('value')->nullable();
            // $table->string('related_table')->nullable();
            // $table->boolean('nullable')->default(false);
            $table->boolean('hidden')->default(false);
            $table->boolean('disabled')->default(false);
        });

        Schema::create('configuration_groups', function (Blueprint $table) {
            $table->id();
            $table->text('key');
            $table->boolean('hidden')->default(false);
            $table->boolean('disabled')->default(false);
        });

        Schema::create('configuration_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('configuration_id');
            $table->unsignedBigInteger('configuration_group_id');

            # relationships
            $table->foreign('configuration_id')->references('id')->on('configurations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('configuration_group_id')->references('id')->on('configuration_groups')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('configuration_group');
        Schema::dropIfExists('configuration_groups');
        Schema::dropIfExists('configurations');
    }
}
