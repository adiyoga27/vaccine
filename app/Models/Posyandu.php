<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Posyandu extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'village_id',
        'name',
        'address'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['village_id', 'name', 'address']);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function vaccineSchedules()
    {
        return $this->hasMany(VaccineSchedule::class);
    }
}
