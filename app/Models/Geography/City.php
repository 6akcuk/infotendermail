<?php namespace App\Models\Geography;

use Illuminate\Database\Eloquent\Model;

class City extends Model {

	protected $fillable = ['country_id', 'name', 'phonecode'];


  /**
   * Получить страну, к которой прикреплен город.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function country() {
    return $this->belongsTo('App\Models\Geography\Country');
  }

}
