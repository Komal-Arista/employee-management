<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'joining_date', 'department_id', 'profile_photo'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo
            ? Storage::disk('public')->url($this->profile_photo)
            : null;
    }
}
