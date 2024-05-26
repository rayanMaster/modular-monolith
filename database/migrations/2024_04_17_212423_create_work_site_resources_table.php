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
        Schema::create('resource_work_site', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_site_id')->constrained('work_sites');
            $table->foreignId('resource_id')->constrained('resources');
            $table->integer('quantity');
            $table->decimal('price', 8, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_work_site');
    }
};
