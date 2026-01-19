<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Spatie\Activitylog\Traits\LogsActivity;
    use SoftDeletes;

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

    /**
     * Generate URL-safe slug from patient data
     */
    public function getSlug(): string
    {
        $slug = strtolower($this->mother_name . '-' . $this->name);
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    /**
     * Get the public access URL for this patient
     */
    public function getAccessUrl(): string
    {
        return url('/peserta/' . $this->date_birth->format('Y-m-d') . '/' . $this->getSlug());
    }
}
