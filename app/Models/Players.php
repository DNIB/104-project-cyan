<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Players extends Model
{
    /**
     * 與模型關聯的資料表。
     *
     * @var string
     */
    protected $table = 'player';

    public function trips()
    {
        $trips_participated = $this->trip_participate()->get();
        $trips = [];
        foreach ($trips_participated as $trip) {
            $trips[] = $trip->trip;
        }
        return $trips;
    }

    /**
     * 建立與會員的關聯
     * 
     * @return User
     */
    public function user()
    {
        return $this->hasOne(
            User::class,
            'id',
            'user_id'
        );
    }

    /**
     *  建立與 TripParticipates 的一對多關聯
     * 
     * @return TripParticipates
     */
    public function trip_participate()
    {
        return $this->hasMany(
            TripParticipates::class,
            'participate_id',
            'id'
        );
    }
}
