<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sos extends Model
{
    protected $attributes = [
        'attended_by' => null,
    ];

    protected $fillable = [
        'description',
        'latitude',
        'longitude',
        'status',
        'image_path',
        'attended_by',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }
}
