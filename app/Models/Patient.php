<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Spatie\Activitylog\Traits\LogsActivity;

    protected $fillable = [
        'user_id', 'village_id', 'name', 'mother_name', 'date_birth', 'address', 'gender', 'phone'
    ];

    protected $casts = [
        'date_birth' => 'date',
    ];

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()->logAll();
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vaccinePatients()
    {
        return $this->hasMany(VaccinePatient::class);
    }
}
