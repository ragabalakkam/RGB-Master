<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppsClientsTable extends Migration
{
    public function up()
    {
        Schema::create('apps_clients', function (Blueprint $table) 
        {
            # app-specific
            $table->string('id')->primary();
            $table->string('secret');
            $table->text('name');
            $table->text('configurations')->nullable();
            
            # domain
            $table->string('domain')->nullable();
            $table->string('root_dir')->nullable();

            # creator
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            
            # client
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');

            # app
            $table->unsignedBigInteger('app_id');
            $table->foreign('app_id')->references('id')->on('apps')->onUpdate('cascade')->onDelete('cascade');
            
            # version
            $table->unsignedBigInteger('version_id')->nullable();
            $table->foreign('version_id')->references('id')->on('versions')->onUpdate('cascade')->onDelete('set null');
            
            # business type
            $table->unsignedBigInteger('business_type_id')->nullable();
            $table->foreign('business_type_id')->references('id')->on('business_types')->onUpdate('cascade')->onDelete('set null');

            # database
            $table->string('db_driver')->nullable();
            $table->string('db_host')->nullable();
            $table->string('db_database')->nullable();
            $table->string('db_username')->nullable();
            $table->string('db_password')->nullable();

            # flags
            $table->boolean('active')->default(true);

            # active process
            $table->string('active_process')->nullable();
            $table->timestamp('started_process_at')->nullable();
            $table->float('installation_time')->nullable()->default(0);
            $table->float('uninstallation_time')->nullable()->default(0);
            $table->float('update_time')->nullable()->default(0);

            # timestamps
            $table->timestamp('checked_for_updates_at')->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->timestamp('licensed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('apps_clients');
    }
}
