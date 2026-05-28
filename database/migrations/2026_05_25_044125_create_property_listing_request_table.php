<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_listing_request', function (Blueprint $table) {
            $table->string('requestid', 20)->primary();

            $table->string('renterno', 20);
            $table->foreign('renterno')->references('renterno')->on('renter');

            $table->text('street');
            $table->text('area');
            $table->text('city');
            $table->string('postcode', 20);
            $table->string('property_type', 50);
            $table->smallInteger('no_of_rooms');
            $table->decimal('monthly_rate', 10, 2);
            $table->text('main_image')->nullable();
            $table->text('message')->nullable();

            // Pending → Approved or Rejected
            $table->string('status', 20)->default('Pending');

            $table->string('reviewed_by', 20)->nullable();
            $table->foreign('reviewed_by')->references('staffno')->on('staff');
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_listing_request');
    }
};