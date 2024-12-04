<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('postcodes', function (Blueprint $table) {
            $table->id();
            $table->string('postcode')->unique();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedBigInteger('country_id');
            $table->timestamps();

            $table->index(['postcode', 'country_id']);

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::Statement("SET FOREIGN_KEY_CHECKS=0");
        Schema::dropIfExists('postcodes');
        \Illuminate\Support\Facades\DB::Statement("SET FOREIGN_KEY_CHECKS=1");
    }
};
