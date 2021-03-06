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
        return $this->locations()->get()->toArray();
    }

    /**
     * 回傳屬於該行程的地點，並按照其順序
     * 
     * @return array
     */
    public function locationsWithOrder()
    {
        $locations = $this->Triplocations()->OrderBy('trip_order')->get();
        $locations_info = [];

        foreach ( $locations as $location) {
            $locations_info[] = [
                'location' => $location->location()->get()[0],
                'arrival_method' => $location->arrival_method()->get()[0]->name,
                'time' => $location->time,
                'order' => $location->trip_order,
            ];
        }

        return $locations_info;
    }

    /**
     * 刪除該資料庫的資料時，一併刪除與此資料關聯的 TripLocations 以及 TripParticipates
     * 
     * @return void
     */
    public function delete()
    {
        $locations = $this->triplocations();
        $participates = $this->players();

        $locations->delete();
        $participates->delete();
        parent::delete();
    }

    /**
     * 建立與 Player 的一對多關聯
     * 
     * @return Players
     */
    public function players()
    {
        return $this->hasMany(
            Players::class,
            'trip_id',
            'id'
        );
    }
    
    /**
     * 建立與 TripLocations 的一對多關聯
     * 
     * @return TripLocations
     */
    public function triplocations()
    {
        return $this->hasMany(
            TripLocations::class,
            'trip_id',
            'id'
        );
    }

    /**
     * Ralation to Location
     * Many to Many
     * 
     * @return Locations
     */
    public function locations()
    {
        return $this->belongsToMany(
            Locations::class,
            TripLocations::class,
            'trip_id',
            'location_id',
            'id',
            'id',
        );
    }
}
