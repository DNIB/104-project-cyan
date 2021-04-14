<?php

namespace App;

use App\Models\Players;
use App\Models\LocationEditor;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
     * 用來建立與 Player 對應的關聯
     * 
     * @return Players
     */
    public function player()
    {
        return $this->belongsTo(
            Players::class,
            'member_id',
            'id'
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
