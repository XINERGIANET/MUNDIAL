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
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('home_source_match_id')->nullable()->after('group_id')
                ->constrained('matches')->nullOnDelete();
            $table->foreignId('away_source_match_id')->nullable()->after('home_source_match_id')
                ->constrained('matches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('home_source_match_id');
            $table->dropConstrainedForeignId('away_source_match_id');
        });
    }
};
