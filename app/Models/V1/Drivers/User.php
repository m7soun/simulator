<?php

namespace App\Models\V1\Drivers;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'mobile_aplication_kays';

    // relation with driver model
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'sim_id', 'imsi');
    }
}
