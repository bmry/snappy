<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->geometry('location', subtype: 'point');
            $table->boolean('open')->default(false);
            $table->enum('type', ['takeaway', 'shop', 'restaurant']);
            $table->integer('max_delivery_distance');
            $table->timestamps();

            $table->spatialIndex('location'); // Index for geospatial queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};