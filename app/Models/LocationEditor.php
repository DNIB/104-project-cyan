<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationEditor extends Model
{
    /**
     * 與模型關聯的資料表。
     *
     * @var string
     */
    protected $table = 'location_editor';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['location_id', 'user_id', 'isEditor'];

    /**
     * 用來建立與 Location 對應的一對一關聯
     * 
     * @return Locations
     */
    public function locationInfo()
    {
        return $this->hasOne(
            Locations::class,
            'id',
            'location_id'
        );
    }
}
