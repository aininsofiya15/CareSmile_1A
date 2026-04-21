<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->id();
            // Links exactly to the User table
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            // The medical profile fields
            $table->string('phone_number')->nullable();
            $table->date('dob')->nullable();
            $table->text('address')->nullable();
            $table->string('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_profiles');
    }
};