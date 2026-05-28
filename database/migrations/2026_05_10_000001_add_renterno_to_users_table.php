<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Only add the column if it doesn't exist yet
        if (!Schema::hasColumn('users', 'renterno')) {
            $table->string('renterno')->nullable(); // or whatever your original column definition was
        }
    });
}

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['renterno']);
            $table->dropColumn('renterno');
        });
    }
};
