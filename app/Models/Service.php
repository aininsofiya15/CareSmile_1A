<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    
    protected $fillable = [
        'name', 'description', 'price', 'duration_minutes', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // REMOVE or COMMENT OUT this method since Appointment model doesn't exist yet
    // public function appointments()
    // {
    //     return $this->hasMany(Appointment::class);
    // }

    // Scope for active services
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}