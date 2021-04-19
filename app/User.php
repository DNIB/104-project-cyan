<?php

namespace App;

use App\Models\Players;
use App\Models\LocationEditor;
use App\Models\TripParticipates;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use phpDocumentor\Reflection\Types\Parent_;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 用來取出相關的地點
     * 
     * @param boolean $isEditor
     * 
     * @return array
     */
    public function locations( $isEditor = false )
    {
        $locationRelated = $this->locationRelated();
        if ( $isEditor ) {
            $locationRelated = $locationRelated->where('isEditor', $isEditor)->get();

        } else {
            $locationRelated = $locationRelated->get();
        }

        $ret = [];
        foreach ( $locationRelated as $location) {
            $ret[] = $location->locationInfo()->get()[0];
        }

        return $ret;
    }
    
    /**
     * 回傳所有該名使用者有參加的行程中的地點，並按照其旅行順序
     * 回傳陣列中 key 為 locations 的部分，會回傳行程的資料，其格式為陣列
     * 可按以下 key 取相關的數值 
     * 'location' => Locations
     * 'arrival_method' => 抵達方式
     * 'time' => 抵達時間
     * 'order' => 抵達順序（若無更改，則將會同於 TripLocations 的主鍵
     * 
     * @return array
     */
    public function getTripInfo()
    {
        $trips = $this->trips();

        $locations  = [];
        foreach ( $trips as $trip ) {
            $locations[] = [
                'trip' => $trip,
                'locations' => $trip->locationsWithOrder(),
            ];
        }
        return $locations;
    }

    /**
     * 回傳所有該名使用者參加的行程，並以陣列回傳
     * 
     * @return array
     */
    public function trips()
    {
        $user_id = $this->id;
        $players_of_user = Players::where('user_id', $user_id)->get();

        $ret_trips = [];
        foreach ( $players_of_user as $player ) {
            $trips = $player->trips();
            foreach ( $trips as $trip ) {
                $ret_trips[ $trip->id ] = $trip;
            }
        }
        return $ret_trips;
    }

    /**
     * 在呼叫此函式時，一併刪除 player 資料庫裡的資料
     */
    public function delete()
    {
        $target_player = Players::where('user_id', $this->id);
        $isTargetNotEmpty = count( $target_player->get() );

        if ( $isTargetNotEmpty ) {
            $target_player->delete();
        }
        parent::delete();
    }

    /**
     * 用來建立與 Player 對應的關聯
     * 
     * @return Players
     */
    public function player()
    {
        return $this->belongsTo(
            Players::class,
            'id',
            'user_id'
        );
    }

    /**
     * 用來建立與 LocationEditor 對應的一對多關聯
     * 
     * @return LocationEditor
     */
    public function locationRelated()
    {
        return $this->hasMany(
            LocationEditor::class,
            'user_id',
            'id'
        );
    }
}
