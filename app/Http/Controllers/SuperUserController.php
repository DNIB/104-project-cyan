<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ReadRequest;
use App\Models\Locations;
use App\Models\Trips;
use App\Models\Players;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperUserController extends Controller
{
    use ReadRequest;

    /**
     * 依據傳入的請求，更新使用者的資訊
     * 請求應有資料：
     *   - (integer) id
     *   - (string) name
     *   - (string) email
     *   - (string) password
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function update( Request $request )
    {
        $id = $request->id;
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        $isPasswordEmpty = empty($password);

        $isIdValid = !empty(User::find($id));
        $isNameValid = !empty($name);
        $isEmailValid = filter_var($email, FILTER_VALIDATE_EMAIL);
        $isPasswordValid = ( $isPasswordEmpty || (strlen($password) >= 8) );

        $isRequestValid = $isIdValid && $isNameValid && $isEmailValid && $isPasswordValid;

        if ($isRequestValid ) {
            $user = User::find($id);
            $user->name = $name;
            $user->email = $email;
            if (!$isPasswordEmpty ) {
                $user->password = Hash::make($password);
            }
            $user->save();
            return redirect()->back();
        }
        abort(400);
    }

    /**
     * 依據傳入的請求，刪除使用者的資訊
     * 請求應有資料：
     *   - (integer) delete_id
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function delete( Request $request )
    {
        $id = $request->delete_id;

        $isIdValid = is_numeric($id);
        if ($isIdValid ) {
            $user = User::find($id);
            $isUserExist = !empty($user);
            if ($isUserExist ) {
                $user->delete();
                return redirect()->back();
            } else {
                abort(400);
            }
        }
        abort(400);
    }

    /**
     * 取得所有行程的資料，並回傳至 view 以供顯示
     * 
     * @return view
     */
    public function showAllTrips()
    {
        $column = [
            'id',
            'name',
            'description',
        ];
        $trips = Trips::all();
        $require = [
            true,
            true,
            false,
        ];
        $type = [
            'text',
            'text',
            'text',
        ];

        $ret = [
            'columns' => $column,
            'rows' => $trips,
            'name' => "行程後台管理",
            'type' => 'trip',
            'require' => $require,
            'input_type' => $type,
        ];

        return view('manage.main', $ret);
    }

    /**
     * 取得所有地點的資料，並回傳至 view 以供顯示
     * 
     * @return view
     */
    public function showAllLocations()
    {
        $column = [
            'id',
            'name',
            'description',
            'lat',
            'lng',
        ];
        $locations = Locations::all();
        $require = [
            true,
            true,
            false,
            true,
            true,
        ];
        $type = [
            'text',
            'text',
            'text',
            'text',
            'text',
        ];
        
        $ret = [
            'columns' => $column,
            'rows' => $locations,
            'name' => "地點後台管理",
            'type' => 'location',
            'require' => $require,
            'input_type' => $type,
        ];

        return view('manage.main', $ret);
    }

    /**
     * 取得所有參加者的資料，並回傳至 view 以供顯示
     * 
     * @return view
     */
    public function showAllPlayers()
    {
        $column = [
            'id',
            'name',
            'description',
            'user_id',
            'trip_id',
            'email',
            'phone',
        ];
        $players = Players::all();
        $require = [
            true,
            true,
            false,
            false,
            true,
            false,
            false
        ];
        $type = [
            'text',
            'text',
            'text',
            'number',
            'number',
            'email',
            'tel',
        ];

        $ret = [
            'columns' => $column,
            'rows' => $players,
            'name' => "參加者後台管理",
            'type' => 'player',
            'require' => $require,
            'input_type' => $type,
        ];

        return view('manage.main', $ret);
    }

    /**
     * 按傳入請求及參數，進行資料更新的處理
     * 
     * @param Request $request
     * @param string  $type
     * 
     * @return view
     */
    public function updateData( Request $request, string $type )
    {
        try{
            $target_type = $this->getTargetElement($type);
            $target = $target_type->find($request->id);
            $elements = $request->all();
            
            foreach ( $elements as $key => $value ) {
                $isSkip = ( $key == "_token" ) || ( $key == '_method' ) || ( $key == 'id');
                if ($isSkip ) {
                    continue;
                } else {
                    $target->$key = $value;
                }
            }

            $target->save();
            
            return redirect()->back();

        } catch (Exception $e) {
            abort(403);
        }
    }

     /**
      * 按傳入請求及參數，進行資料刪除的處理
      * 
      * @param Request $request
      * @param string  $type
      * 
      * @return view
      */
    public function deleteData( Request $request, string $type)
    {
        try{
            $target_type = $this->getTargetElement($type);
            $id = $request->id;

            $target = $target_type->find($id);
            $target->delete();

            return redirect()->back();

        } catch (Exception $e) {
            abort(403);
        }
    }

    /**
     * 按傳入的參數，決定回傳的物件種類
     * 
     * @param string $type
     * 
     * @return Object
     */
    private function getTargetElement( string $type )
    {
        switch ( $type ) {
        case 'trip':
            return new Trips;
        case 'location':
            return new Locations;
        case 'player':
            return new Players;
        default:
            abort(404);
        }
    }
}
