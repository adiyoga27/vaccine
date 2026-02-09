<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Spatie\Activitylog\Traits\LogsActivity;

    protected $fillable = ['name', 'minimum_age', 'duration_days', 'is_required'];

    protected $casts = [
        'name' => 'string',
        'minimum_age' => 'integer',
        'duration_days' => 'integer',
        'is_required' => 'boolean',
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
