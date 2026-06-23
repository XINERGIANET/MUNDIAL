<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->boolean('is_welcome_courtesy')->default(false)->after('status');
            $table->index(['tournament_id', 'is_welcome_courtesy']);
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex(['tournament_id', 'is_welcome_courtesy']);
            $table->dropColumn('is_welcome_courtesy');
        });
    }
};
