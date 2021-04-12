<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trips extends Model
{
    /**
     * 與模型關聯的資料表。
     *
     * @var string
     */
    protected $table = 'trip';

    /**
     * 取出所有行程裡的地點，並以 Array 回傳
     * 
     * @return array
     */
    public function getAllLocationInfo()
    {
        $locations = $this->locations()->get();
        $locations_info = [];

        foreach ( $locations as $location) {
            $locations_info[] = $location->location()->get()[0];
        }

        return $locations_info;
    }

    /**
     * 建立與 TripLocations 的一對多關聯
     * 
     * @return TripLocations
     */
    public function locations()
    {
        return $this->hasMany(
            TripLocations::class,
            'trip_id',
            'id'
        );
    }

    /**
     * 建立與 TripParticipates 的一對多關聯
     * 
     * @return TripParticipates
     */
    public function participates()
    {
        return $this->hasMany(
            TripParticipates::class,
            'trip_id',
            'id'
        );
    }
}
