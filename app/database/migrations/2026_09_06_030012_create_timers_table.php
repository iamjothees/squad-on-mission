<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timers', function (Blueprint $table) {
            $table->id();
            $table->morphs('timerable');
            $table->string('purpose')->nullable();
            $table->integer('accumulated_seconds')->default(0);
            $table->boolean('is_running')->default(false);
            $table->bigInteger('last_started_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timers');
    }
};
