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
        Schema::table('products', function (Blueprint $table) {
            $table->string('title_en')->after('id');
            $table->string('title_ar')->after('title_en');
            $table->text('description_en')->nullable()->after('title_ar');
            $table->text('description_ar')->nullable()->after('description_en');
            $table->decimal('price', 10, 2)->after('description_ar');
            $table->string('slug')->unique()->after('price');
            $table->string('primary_image')->nullable()->after('slug');
            $table->json('other_images')->nullable()->after('primary_image');
            $table->foreignId('user_id')->nullable()->after('other_images')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'title_en',
                'title_ar',
                'description_en',
                'description_ar',
                'price',
                'slug',
                'primary_image',
                'other_images',
                'user_id'
            ]);
        });
    }
};
