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
        Schema::table('users', function (Blueprint $table) {
            // Split name into first_name and last_name
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            
            // Verification fields
            $table->string('verification_code', 4)->nullable()->after('email_verified_at');
            $table->timestamp('verification_code_expires_at')->nullable()->after('verification_code');
            $table->boolean('is_active')->default(false)->after('verification_code_expires_at');
            
            // Login attempts tracking
            $table->integer('login_attempts')->default(0)->after('is_active');
            $table->timestamp('blocked_until')->nullable()->after('login_attempts');
            
            // Make email nullable (can use phone instead)
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'verification_code',
                'verification_code_expires_at',
                'is_active',
                'login_attempts',
                'blocked_until'
            ]);
        });
    }
};
