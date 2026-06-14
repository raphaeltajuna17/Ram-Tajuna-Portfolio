<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('hunger')->default(100);
            $table->integer('energy')->default(100);
            $table->integer('hygiene')->default(100);
            $table->integer('happiness')->default(100);
            $table->boolean('is_sick')->default(false);
            $table->timestamp('last_interacted_at')->useCurrent();
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cats');
    }
};