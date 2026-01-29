<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Spatie\Activitylog\Traits\LogsActivity;

    protected $fillable = ['name', 'posyandu_name', 'address'];

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()->logAll();
    }

    public function schedules()
    {
        return $this->hasMany(VaccineSchedule::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function vaccinePatients()
    {
        return $this->hasMany(VaccinePatient::class);
    }

    public function posyandus()
    {
        return $this->hasMany(Posyandu::class);
    }
}
