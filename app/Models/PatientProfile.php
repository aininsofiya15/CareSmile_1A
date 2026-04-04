<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientProfile extends Model
{
    // These are the columns we are allowing the user to fill out on the profile page
    protected $fillable = [
        'user_id', 
        'phone_number', 
        'dob', 
        'address', 
        'allergies', 
        'medications', 
        'emergency_contact_name', 
        'emergency_contact_phone'
    ];

    // This is the reverse relationship back to the User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}