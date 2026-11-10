<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!class_exists('CreateSchoolsTable')) {
    class CreateSchoolsTable extends Migration
    {
        public function up()
        {
            if (!Schema::hasTable('schools')) {
                Schema::create('schools', function (Blueprint $table) {
                    $table->string('codMod')->primary();
                    $table->string('idDistrito')->nullable()->index();
                    $table->string('nombreCenEdu')->nullable();
                    $table->string('cenPob')->nullable();
                    $table->string('dirCen')->nullable();
                    $table->string('director')->nullable();
                    $table->string('dis_crecer')->nullable();
                    $table->string('dis_juntos')->nullable();
                    $table->timestamps();
                });
            }
        }

        public function down()
        {
            Schema::dropIfExists('schools');
        }
    }
}
