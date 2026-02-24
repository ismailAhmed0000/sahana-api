<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sos extends Model
{
    protected $fillable = [
        'description',
        'latitude ',
        'longitude',
        'status'
    ];

    public function attachable()
    {
        return $this->morphTo();
    }
}
