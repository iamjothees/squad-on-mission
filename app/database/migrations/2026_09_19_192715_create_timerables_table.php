<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timerables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timer_id')->constrained()->cascadeOnDelete();
            $table->morphs('timerable');
            $table->timestamps();
            
            $table->unique(['timer_id', 'timerable_type', 'timerable_id']);
        });

        Schema::table('timers', function (Blueprint $table) {
            $table->dropColumn(['timerable_type', 'timerable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('timers', function (Blueprint $table) {
            $table->string('timerable_type')->nullable();
            $table->unsignedBigInteger('timerable_id')->nullable();
            $table->index(['timerable_type', 'timerable_id']);
        });

        Schema::dropIfExists('timerables');
    }
};
