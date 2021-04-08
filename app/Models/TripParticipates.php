<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripParticipates extends Model
{
    /**
     * 與模型關聯的資料表。
     *
     * @var string
     */
    protected $table = 'trip_participate';

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
     * 建立與 Players 對應的關聯
     */
    public function player()
    {
        return $this->belongsTo(
            Players::class,
            'id',
            'participate_id'
        );
    }
}
