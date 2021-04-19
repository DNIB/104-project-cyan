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

    /**
     * 建立與會員的關聯
     * 
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    /**
     *  建立與 Trip 的一對一關聯
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
}
