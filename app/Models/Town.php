<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    //
    protected $fillable = ['region_id', 'name'];

    public function region()
    {
        return $this->belongsTo('App\Models\Region');
    }
}
