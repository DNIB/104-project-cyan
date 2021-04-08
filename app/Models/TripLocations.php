<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
