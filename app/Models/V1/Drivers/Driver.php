<?php

namespace App\Models\V1\Drivers;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'car';

    // relation with User model

    public function user()
    {
        return $this->hasOne(User::class, 'sim_id', 'imsi');
    }
}
