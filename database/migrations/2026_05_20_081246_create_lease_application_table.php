<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the lease_application table.
     * A renter can apply for a property with or without a prior viewing.
     * Staff review the application and either approve (auto-creating a lease) or reject it.
     */
    public function up(): void
    {
        // Check if pgAdmin/Postgres already has this table
        if (!Schema::hasTable('lease_application')) {
            Schema::create('lease_application', function (Blueprint $table) {
                $table->string('applicationid', 20)->primary();

                $table->string('renterno', 20);
                $table->foreign('renterno')->references('renterno')->on('renter');

                $table->string('propertyno', 20);
                $table->foreign('propertyno')->references('propertyno')->on('property');

                // Optional: linked viewing (if renter viewed the property first)
                $table->string('viewingid', 20)->nullable();
                $table->foreign('viewingid')->references('viewingid')->on('viewing');

                $table->date('preferred_start_date');
                $table->text('message')->nullable();

                // Pending → Approved or Rejected by staff
                $table->string('status', 20)->default('Pending');

                // Staff who reviewed the application
                $table->string('reviewed_by', 20)->nullable();
                $table->foreign('reviewed_by')->references('staffno')->on('staff');
                $table->timestamp('reviewed_at')->nullable();

                $table->timestamps(); // created_at, updated_at
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_application');
    }
};