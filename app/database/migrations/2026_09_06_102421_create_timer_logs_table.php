<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timer_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('started_at'); // js timestamp
            $table->bigInteger('stopped_at')->nullable(); // js timestamp
            $table->integer('duration_seconds')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timer_logs');
    }
};
