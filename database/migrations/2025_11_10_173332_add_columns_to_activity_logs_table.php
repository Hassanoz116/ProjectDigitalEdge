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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->string('action')->after('user_id');
            $table->string('model_type')->nullable()->after('action');
            $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            $table->text('description')->nullable()->after('model_id');
            $table->json('properties')->nullable()->after('description');
            $table->string('ip_address', 45)->nullable()->after('properties');
            $table->text('user_agent')->nullable()->after('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'action',
                'model_type',
                'model_id',
                'description',
                'properties',
                'ip_address',
                'user_agent'
            ]);
        });
    }
};
