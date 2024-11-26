<?php

use App\Models\Item;
use App\Models\Worksite;
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
        Schema::create('worksite_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Worksite::class, 'worksite_id');
            $table->foreignIdFor(Item::class, 'item_id');
            $table->integer('quantity');
            $table->decimal('price', 8, 2);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worksite_items');
    }
};
