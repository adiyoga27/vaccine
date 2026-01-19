<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'content',
        'variables' // JSON or comma-separated string
    ];

    public static function parse($content, $patient, $extras = [])
    {
        // Standard Patient Fields
        $fields = ['name', 'mother_name', 'date_birth', 'address', 'gender', 'phone'];
        foreach ($fields as $field) {
            $content = str_replace("[$field]", $patient->$field ?? '-', $content);
        }
        
        // Relations
        $content = str_replace('[village_name]', $patient->village->name ?? '-', $content);
        
        // Extras
        foreach ($extras as $key => $value) {
            $content = str_replace("[$key]", $value, $content);
        }
        
        return $content;
    }
}
