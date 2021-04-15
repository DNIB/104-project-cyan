<?php

namespace App;

use App\Models\Players;
use App\Models\LocationEditor;
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
                'locations' => $trip->locationsWithOrder()
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
        $player = $this->player()->get()[0];
        $trips = $player->trips();
        return $trips;
    }

    /**
     * 在呼叫此函式時，一併新增或更新 player 資料庫裡的資料
     */
    public function save(array $options = [])
    {
        parent::save( $options );

        $target_player = Players::where('user_id', $this->id)->get();
        $isPlayerExist = count( $target_player ) > 0;

        if ( $isPlayerExist ) {
            $player = $target_player[0];
        } else {
            $player = new Players;
        }
        $player->name = $this->name;
        $player->email = $this->email;
        $player->user_id = $this->id;
        $player->save();
    }

    /**
     * 在呼叫此函式時，一併刪除 player 資料庫裡的資料
     */
    public function delete()
    {
        $target_player = Players::where('user_id', $this->id)->get();
        $player = $target_player[0];

        $player->delete();
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
