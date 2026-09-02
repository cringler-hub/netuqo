<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('area', ['business', 'private'])->nullable();
            $table->enum('status', ['open', 'done'])->default('open');
            $table->timestamp('due_at')->nullable();
            $table->string('source')->default('manual');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
