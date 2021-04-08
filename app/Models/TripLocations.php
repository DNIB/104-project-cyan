<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripLocations extends Model
{
    /**
     * 與模型關聯的資料表。
     *
     * @var string
     */
    protected $table = 'trip_location';

    /**
     * 用於在行程中添加地點，加入於行程尾端
     * 
     * @param TripLocation $data
     * 
     * @return void
     */
    public function appendLocation(TripLocations $data)
    {
        $trip_id = $data->trip_id;
        $trip_same_id = $this->where('trip_id', $trip_id)->get();

        $trip_same_id_count = count($trip_same_id);

        $data->trip_order =$trip_same_id_count;
        $data->save();

        return;
    }

    /**
     * 建立與 Trips 對應一對多的關聯
     * 
     * @return Trips
     */
    public function trip()
    {
        return $this->belongsTo(
            Trips::class,
            'id',
            'trip_id'
        );
    }

    /**
     * 建立與 ArrivalMethods 的一對一關聯
     * 
     * @return ArrivalMethods
     */
    public function arrival_method()
    {
        return $this->hasOne(
            ArrivalMethods::class,
            'id',
            'arrival_method'
        );
    }

    /**
     * 建立與 Locations 的一對一關聯
     * 
     * @return Locations
     */
    public function location()
    {
        return $this->belongsTo(
            Locations::class,
            'id',
            'location_id'
        );
    }
}
