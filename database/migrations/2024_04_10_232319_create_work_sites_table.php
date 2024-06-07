<?php

use App\Enums\WorkSiteStatusesEnum;
use App\Models\WorkSite;
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
            $table->foreignIdFor(\App\Models\Customer::class)->nullable();
            $table->foreignIdFor(\App\Models\WorkSiteCategory::class)->nullable();
            $table->foreignIdFor(WorkSite::class)->nullable();
            $table->decimal('starting_budget', 8, 2);
            $table->decimal('cost', 8, 2);
            $table->foreignIdFor(\App\Models\Address::class)->nullable();
            $table->integer('workers_count')->default(0);
            $table->date('receipt_date')->nullable();
            $table->date('starting_date')->nullable();
            $table->date('deliver_date')->nullable();
            $table->tinyInteger('status_on_receive')->default(WorkSiteStatusesEnum::SCRATCH->value);

            $table->timestamps();
            $table->softDeletes();
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
