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
        Schema::create('work_sites', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('category_id')->nullable()->constrained('work_site_categories');
            $table->unsignedInteger('main_worksite')->nullable();
            $table->decimal('starting_budget', 8, 2);
            $table->decimal('cost', 8, 2);
            $table->unsignedInteger('address')->nullable();
            $table->integer('workers_count');
            $table->date('receipt_date');
            $table->date('starting_date');
            $table->date('deliver_date');
            $table->tinyInteger('status_on_receive');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_sites');
    }
};
