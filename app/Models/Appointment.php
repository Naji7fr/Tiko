<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'dentist_id',
        'appointment_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function dentist()
    {
        return $this->belongsTo(User::class, 'dentist_id');
    }
}
