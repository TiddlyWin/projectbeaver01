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
        Schema::table('users', static function (Blueprint $table) {
            $table->text('eve_token')->nullable();
            $table->text('eve_refresh_token')->nullable();
            $table->timestamp('eve_token_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn([
                'eve_token',
                'eve_refresh_token',
                'eve_token_expires_at'
            ]);
        });
    }
};
