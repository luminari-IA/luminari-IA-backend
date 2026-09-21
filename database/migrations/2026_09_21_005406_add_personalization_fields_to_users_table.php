<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo_path')->nullable();
            $table->string('theme_color')->default('#6c63ff');
            $table->string('font_family')->default('Inter, sans-serif');
            $table->string('border_style')->default('rounded'); // 'rounded', 'smooth', 'square'
            $table->boolean('reduced_animations')->default(false);
            $table->string('tts_speed')->default('normal'); // 'slow', 'normal', 'fast'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo_path',
                'theme_color',
                'font_family',
                'border_style',
                'reduced_animations',
                'tts_speed'
            ]);
        });
    }
};
