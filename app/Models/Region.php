<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    //
    protected $fillable = ['country_id', 'name'];

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }
}
