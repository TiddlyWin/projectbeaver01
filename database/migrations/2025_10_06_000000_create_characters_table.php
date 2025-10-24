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
        Schema::create('characters', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('eve_character_id')->unique();
            $table->string('name');
            $table->string('portrait_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::table('users', static function (Blueprint $table) {
            $table->foreignId('main_character_id')->nullable()->after('password')->constrained('characters')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('main_character_id');
        });

        Schema::dropIfExists('characters');
    }
};


