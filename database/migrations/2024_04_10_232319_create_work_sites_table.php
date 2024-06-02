<?php

use App\Enums\WorkSiteStatusesEnum;
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
            $table->integer('workers_count')->default(0);
            $table->date('receipt_date')->nullable();
            $table->date('starting_date')->nullable();
            $table->date('deliver_date')->nullable();
            $table->tinyInteger('status_on_receive')->default(WorkSiteStatusesEnum::SCRATCH->value);

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
