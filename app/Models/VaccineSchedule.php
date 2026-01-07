<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccineSchedule extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Spatie\Activitylog\Traits\LogsActivity;

    protected $fillable = [
        'village_id',
        'posyandu_id',
        'scheduled_at'
    ];

    protected $casts = [
        'scheduled_at' => 'date',
    ];

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()->logAll();
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class);
    }

    public function vaccines()
    {
        return $this->belongsToMany(Vaccine::class, 'schedule_vaccine');
    }
}
