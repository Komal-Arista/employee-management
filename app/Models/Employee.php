<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'joining_date', 'department_id', 'profile_photo'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
