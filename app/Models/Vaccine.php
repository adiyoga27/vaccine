<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Spatie\Activitylog\Traits\LogsActivity;

    protected $fillable = ['name', 'minimum_age', 'duration_days'];

    protected $casts = [
        'name' => 'string',
        'minimum_age' => 'integer',
        'duration_days' => 'integer',
    ];

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()->logAll();
    }

    public function vaccinePatients()
    {
        return $this->hasMany(VaccinePatient::class);
    }
}
