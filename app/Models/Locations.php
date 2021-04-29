<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Locations extends Model
{
    /**
     * 與模型關聯的資料表。
     *
     * @var string
     */
    protected $table = 'location';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'lat', 'lng'];

    /**
     * 按傳入的作者編號，儲存作者資訊進 Location 裏
     * 預設作者為超級使用者
     * 
     * @param integer $author = 1
     */
    public function appendLocation( $author=1 )
    {
        $this->save();
        
        $locationRelated = new LocationEditor;

        $locationRelated->location_id = $this->id;
        $locationRelated->user_id = $author;
        $locationRelated->isEditor = true;
        $locationRelated->save();
    }

    /**
     * 刪除地點時，一併刪除關聯資料
     */
    public function delete()
    {
        $trip_location = $this->trip_location();
        $trip_location->delete();
        $editor = $this->locationEditor();
        $editor->delete();
        parent::delete();
    }

    /**
     * 建立與 TripLocation 的一對多連結
     * 
     * @return TripLocations
     */
    public function trip_location()
    {
        return $this->hasMany(
            TripLocations::class,
            'location_id',
            'id'
        );
    }

    /**
     * 建立與 LocationEditor 的關聯
     * 
     * @return LocationEditor
     */
    public function locationEditor()
    {
        return $this->belongsTo(
            LocationEditor::class,
            'id',
            'location_id'
        );
    }
}
