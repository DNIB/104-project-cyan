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
     * 避免更新資料的時候，出現重複資料
     */
    public function save(array $options = [])
    {
        $player = $this->participate_id;
        $trip = $this->trip_id;
        $target = TripParticipates::where('participate_id', $player);
        $isTargetNotEmpty = count( $target->get() ) > 0;

        if ( $isTargetNotEmpty ) {
            $target = $target->where('trip_id', $trip)->get();
            $isTargetNotEmpty = count( $target ) > 0;
        }

        if ( $isTargetNotEmpty ) {
            return;
        } else {
            parent::save();
        }
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
            'trip_id',
            'id'
        );
    }

    /**
     * 建立與 Players 對應的關聯
     */
    public function player()
    {
        return $this->belongsTo(
            Players::class,
            'participate_id',
            'id'
        );
    }
}
