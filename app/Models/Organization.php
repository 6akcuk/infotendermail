<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    //
    protected $fillable = [
        'name',
        'url',
        'level',
        'inn',
        'kpp',
        'ogrn',
        'okato',
        'country_id',
        'region_id',
        'town_id',
        'postal_code',
        'address',
        'contact_name',
        'contact_address',
        'contact_phone',
        'contact_fax',
        'contact_email'
    ];

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function region()
    {
        return $this->belongsTo('App\Models\Region');
    }

    public function town()
    {
        return $this->belongsTo('App\Models\Town');
    }
}
