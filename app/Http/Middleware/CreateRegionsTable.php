<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionsTable extends Migration
{
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->string('idRegion')->primary();
            $table->string('nombreRegion');
            $table->decimal('pointX', 10, 7);
            $table->decimal('pointY', 10, 7);
            $table->integer('zoom');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('regions');
    }
}
