<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany; 
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// ✅ ADD THIS

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isPatient(): bool
    {
        return $this->role === Role::Patient;
    }

    public function isDentist(): bool
    {
        return $this->role === Role::Dentist;
    }

    public function hasRole(Role $role): bool
{
    // We compare the '.value' (the string 'admin') to be safe
    return $this->role->value === $role->value;
}

    public function doctorSchedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id');
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(PatientProfile::class);
    }
}
