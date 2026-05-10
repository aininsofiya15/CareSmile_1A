<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('doctor_schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('doctor_schedules', 'status')) {
                if (Schema::hasColumn('doctor_schedules', 'is_active')) {
                    $table->string('status', 30)->default('active')->after('is_active');
                } else {
                    $table->string('status', 30)->default('active')->after('working_date');
                }
            }

            if (! Schema::hasColumn('doctor_schedules', 'status_updated_automatically')) {
                $table->boolean('status_updated_automatically')->default(false)->after('status');
            }
        });

        if (Schema::hasColumn('doctor_schedules', 'is_active') && Schema::hasColumn('doctor_schedules', 'status')) {
            DB::table('doctor_schedules')
                ->where('is_active', false)
                ->update(['status' => 'inactive']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('doctor_schedules', 'status_updated_automatically')) {
                $table->dropColumn('status_updated_automatically');
            }

            if (Schema::hasColumn('doctor_schedules', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
