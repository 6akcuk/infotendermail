<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    //
    protected $fillable = [
        'organization_id',
        'system_id',
        'name',
        'link',
        'status',
        'type',
        'price',
        'finished_at',
        'results_at'
    ];

    protected $dates = ['finished_at', 'results_at', 'created_at', 'updated_at'];

    public function organization()
    {
        return $this->belongsTo('App\Models\Organization');
    }
}
