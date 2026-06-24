<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sos extends Model
{
    protected $attributes = [
        'attended_by' => null,
    ];

    protected $appends = [
        'image_url',
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

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : null;
    }
}
