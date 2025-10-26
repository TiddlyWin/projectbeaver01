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
        Schema::table('characters', static function (Blueprint $table) {
            $table->string('owner_hash', 32)->nullable()->after('eve_character_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters', static function (Blueprint $table) {
            $table->dropColumn('owner_hash');
        });
    }
};
