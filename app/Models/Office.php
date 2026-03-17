<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Office extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'address'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function villages()
    {
        return $this->belongsToMany(Village::class, 'office_village');
    }

    public function admins()
    {
        return $this->hasMany(User::class)->where('role', 'admin');
    }
}
