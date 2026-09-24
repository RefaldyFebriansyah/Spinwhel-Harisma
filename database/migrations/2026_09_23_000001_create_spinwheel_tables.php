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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon')->default('sparkles');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('wheel_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('class_level', 20)->default('Kelas X');
            $table->string('color', 20)->default('#5A3825');
            $table->string('text_color', 20)->default('#FFFFFF');
            $table->integer('weight')->default(1);
            $table->boolean('is_active')->default(true);
            $table->integer('times_won')->default(0);
            $table->timestamps();
        });

        Schema::create('spin_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('wheel_item_id')->nullable()->constrained()->onDelete('set null');
            $table->string('item_title');
            $table->string('class_level', 20)->nullable();
            $table->string('executor_name')->default('Panitia / Admin');
            $table->timestamp('spun_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('app_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spin_histories');
        Schema::dropIfExists('wheel_items');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('app_settings');
    }
};
