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
        Schema::dropIfExists('services');
    }

    public function down(): void
    {
        // This allows you to "undo" the delete later if you change your mind
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
