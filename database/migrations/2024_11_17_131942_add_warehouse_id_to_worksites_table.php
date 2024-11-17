<?php

use App\Models\Warehouse;
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
        Schema::table('worksites', function (Blueprint $table) {
            $table->foreignIdFor(Warehouse::class)->nullable()->constrained('warehouses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worksites', function (Blueprint $table) {
            $table->dropForeignIdFor(Warehouse::class);
        });
    }
};
