<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccinePatient extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Spatie\Activitylog\Traits\LogsActivity;

    protected $fillable = [
        'village_id', 'posyandu_id', 'patient_id', 'vaccine_id', 'request_date', 'vaccinated_at', 'status'
    ];

    protected $casts = [
        'request_date' => 'date',
        'vaccinated_at' => 'datetime',
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

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class);
    }
}
