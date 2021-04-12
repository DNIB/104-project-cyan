<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Locations extends Model
{
    /**
     * 與模型關聯的資料表。
     *
     * @var string
     */
    protected $table = 'location';

    /**
     * 建立與 TripLocation 的一對多連結
     * 
     * @return TripLocations
     */
    public function trip_location()
    {
        return $this->hasMany(
            TripLocations::class,
            'location_id',
            'id'
        );
    }
}
