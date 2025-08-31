<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Na migration
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('users', 'verification_code')) {
                $table->string('verification_code')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Only drop columns if they exist
            if (Schema::hasColumn('users', 'verification_code')) {
                $table->dropColumn('verification_code');
            }
            if (Schema::hasColumn('users', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
        });
    }
};
