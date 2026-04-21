<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorSchedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'doctor_id',
        'working_date',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'slot_duration',
        'notes',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'working_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'break_start' => 'datetime:H:i',
            'break_end' => 'datetime:H:i',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the doctor (user) that owns this schedule.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get all time slots for this schedule.
     */
    public function slots(): HasMany
    {
        return $this->hasMany(ScheduleSlot::class, 'schedule_id');
    }

    /**
     * Generate time slots based on schedule working hours.
     *
     * Process:
     * 1. Delete all existing slots for this schedule
     * 2. Calculate slot times from start_time to end_time
     * 3. Skip slots that fall within break time
     * 4. Create new slots with is_available = true
     *
     * Example:
     * - Work: 09:00 - 17:00
     * - Break: 12:00 - 13:00
     * - Duration: 30 minutes
     * - Slots: 09:00-09:30, 09:30-10:00, ..., 11:30-12:00, 13:00-13:30, ..., 16:30-17:00
     */
    public function generateSlots(): void
    {
        // Step 1: Remove old slots before generating new ones
        ScheduleSlot::where('schedule_id', $this->id)->delete();

        // Step 2: Parse times for calculation
        $currentSlotStart = strtotime($this->start_time);
        $workEnd = strtotime($this->end_time);
        $slotDurationSeconds = $this->slot_duration * 60; // Convert minutes to seconds

        // Step 3: Parse break times if provided
        $breakStart = null;
        $breakEnd = null;

        if ($this->break_start && $this->break_end) {
            $breakStart = strtotime($this->break_start);
            $breakEnd = strtotime($this->break_end);
        }

        // Step 4: Generate slots
        while ($currentSlotStart < $workEnd) {
            $currentSlotEnd = $currentSlotStart + $slotDurationSeconds;

            // Don't create slot if it extends beyond work hours
            if ($currentSlotEnd > $workEnd) {
                break;
            }

            // Check if this slot falls within break time
            $shouldCreateSlot = true;

            if ($breakStart !== null && $breakEnd !== null) {
                // Skip slot if it starts during break OR overlaps with break
                if (
                    ($currentSlotStart >= $breakStart && $currentSlotStart < $breakEnd) ||
                    ($currentSlotEnd > $breakStart && $currentSlotEnd <= $breakEnd) ||
                    ($currentSlotStart <= $breakStart && $currentSlotEnd >= $breakEnd)
                ) {
                    $shouldCreateSlot = false;
                }
            }

            // Create the slot if it's valid
            if ($shouldCreateSlot) {
                ScheduleSlot::create([
                    'schedule_id' => $this->id,
                    'start_time' => date('H:i:s', $currentSlotStart),
                    'end_time' => date('H:i:s', $currentSlotEnd),
                    'is_available' => true,
                ]);
            }

            // Move to next slot
            $currentSlotStart = $currentSlotEnd;
        }
    }

    /**
     * Check if this schedule overlaps with another schedule for the same doctor on the same date.
     *
     * Logic:
     * - Check if same doctor (doctor_id)
     * - Check if same working date
     * - Exclude current schedule when updating (id != current)
     *
     * Returns: true if overlap exists, false if no overlap
     */
    public function hasOverlap(): bool
    {
        return self::where('doctor_id', $this->doctor_id)
            ->where('working_date', $this->working_date)
            ->where('id', '!=', $this->id)
            ->exists();
    }

    /**
     * Get count of available (unbooked) slots.
     */
    public function availableSlotsCount(): int
    {
        return $this->slots()->where('is_available', true)->count();
    }

    /**
     * Get count of booked (unavailable) slots.
     */
    public function bookedSlotsCount(): int
    {
        return $this->slots()->where('is_available', false)->count();
    }
}
