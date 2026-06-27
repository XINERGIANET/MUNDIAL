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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('banner_path')->nullable();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->index();
            $table->string('status')->default('draft')->index();
            $table->decimal('entry_fee', 12, 2)->nullable();
            $table->string('currency', 8)->default('PEN');
            $table->string('payment_whatsapp_number')->nullable();
            $table->string('payment_yape_number')->nullable();
            $table->string('payment_qr_path')->nullable();
            $table->text('payment_message')->nullable();
            $table->text('rules')->nullable();
            $table->unsignedInteger('exact_score_points')->default(5);
            $table->unsignedInteger('correct_result_points')->default(3);
            $table->unsignedInteger('wrong_prediction_points')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
