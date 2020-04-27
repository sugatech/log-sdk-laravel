<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogHashes5ea65b1e63ba9 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_hashes', function (Blueprint $table) {
            $table->id();
            $table->string('hash')->unique();
            $table->string('type');
            $table->unsignedSmallInteger('version');
            $table->jsonb('keys');
            $table->timestamps();

            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_hashes');
    }
}
