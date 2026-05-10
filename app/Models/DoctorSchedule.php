<?php

namespace App\Models;

use App\Services\ScheduleSlotService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorSchedule extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_FULLY_BOOKED = 'fully_booked';

    public const STATUS_UNAVAILABLE = 'unavailable';

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
        'status',
        'status_updated_automatically',
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
            'status_updated_automatically' => 'boolean',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_FULLY_BOOKED,
            self::STATUS_UNAVAILABLE,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_FULLY_BOOKED => 'Fully Booked',
            self::STATUS_UNAVAILABLE => 'Unavailable',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? 'Active';
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'status-active',
            self::STATUS_FULLY_BOOKED => 'status-fully-booked',
            self::STATUS_UNAVAILABLE => 'status-unavailable',
            default => 'status-inactive',
        };
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isActiveStatus(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isBookable(): bool
    {
        return $this->isActiveStatus() && (bool) $this->is_active;
    }

    public function isFullyBooked(): bool
    {
        return $this->status === self::STATUS_FULLY_BOOKED;
    }

    public function isUnavailable(): bool
    {
        return $this->status === self::STATUS_UNAVAILABLE;
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
        app(ScheduleSlotService::class)->generateSlots($this);
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
