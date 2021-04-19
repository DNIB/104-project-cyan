<?php

namespace App\Http\Controllers;

use App\Models\Players;
use App\Models\TripParticipates;
use App\Models\Trips;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Object_;

class TripPlayerController extends Controller
{
    /**
     * 按傳入的行程編號，回傳行程的參加者的資料，以用來顯示網頁
     * 若登入者無訪問此行程的權限，則回傳錯誤頁面
     * 
     * @param integer $trip_id = 1
     * 
     * @return view
     */
    public function index( $trip_id = -1 )
    {
        $user_id = $this->loginCheck();
        
        $target_trip_participate = $this->getTripParticipatesFromTripId( $trip_id );

        $isRequestValid = $this->checkRequestValid($target_trip_participate, $user_id);

        if ( $isRequestValid ) {
            $trip = Trips::find( $trip_id );
            $ret = [
                'players' => $target_trip_participate,
                'trip' => $trip,
            ];


            return view( 'trip.player_info', $ret);
        } else {
            return view( 'error.invalid_request');
        }
    }

    /**
     * 檢查登入資訊，若有登入則回傳登入 ID，若無登入則導向錯誤
     * 
     * @return integer
     */
    private function loginCheck()
    {
        $isLogin = Auth::check();
        if ( $isLogin ) {
            return Auth::user()->id;
        } else {
            abort(404);
        }
    }

    /**
     * 傳入行程的 ID，回傳屬於此行程 ID 的 TripParticipates
     * 
     * @param integer $trip_id
     * 
     * @return TripParticipates
     */
    private function getTripParticipatesFromTripId( $trip_id )
    {
        return TripParticipates::where('trip_id', $trip_id )
            ->orderBy('participate_id', 'asc')
            ->get();
    }

    /**
     * 確認以下事項：
     *   - 該行程參加表是否存在
     *   - 該使用者 ID 是否可以存取該行程參加表
     * 若皆成立，回傳 true；反之回傳 false
     * 
     * @param Object $trip_participate
     * @param integer $user_id
     * 
     * @return boolean
     */
    private function checkRequestValid( Object $trip_participate, $user_id )
    {
        $isNotEmpty = count( $trip_participate ) > 0;

        if ( $isNotEmpty ) {
            $player_id = Players::where('user_id', $user_id)->get()[0]->id;
            $find_user_in_target = $trip_participate->where('participate_id', $player_id);

            return count( $find_user_in_target ) > 0;
        } else {
            return false;
        }
    }

    /**
     * 建立新的 Player
     * 傳入的 Request 應有以下資料：
     *   - (integer) trip_id
     *   - (string) name
     *   - (string) description (可為空)
     *   - (string) email (可為空)
     *   - (string) phone (可為空)
     * 若該 email 帳戶已存在，則應自動連結
     * 
     * @return view
     */
    public function createPlayer( Request $request )
    {
        $trip_id = $request->trip_id;
        $name = $request->name;
        $desc = $request->desc;
        $email = $request->email;
        $phone = $request->phone;

        //$isNameValid = !empty( $name );
        //$isEmailValid = filter_var($email, FILTER_VALIDATE_EMAIL);

        $isRequestValid = true;

        if ( $isRequestValid ) {
            $player = new Players;
            $player->name = $name;
            $player->description = $desc;
            $player->email = $email;
            $player->phone = $phone;

            $email_checker = $this->checkEmail( $email );
            if ( $email_checker ) {
                $player->user_id = $email_checker;
            }

            $player->save();

            $trip_participate = new TripParticipates;
            $trip_participate->trip_id = $trip_id;
            $trip_participate->participate_id = $player->id;

            $trip_participate->save();

            return $this->index( $trip_id );
        } else {
            return $this->invalidRequest();
        }
        
        return;
    }

    /**
     * 傳入一個 email，嘗試尋找有此 email 的會員
     * 若有，回傳此會員的 id；若無，則回傳 0
     * 
     * @param string $email
     * 
     * @return interger
     */
    private function checkEmail( $email )
    {
        $isEmailNotEmpty = !empty( $email );
        if ( $isEmailNotEmpty ) {
            return $this->findEmail( $email );
        } else {
            return 0;
        }
    }

    /**
     * 從會員尋找有該 email 的會員，並回傳該會員的 id
     * 若無會員，則回傳 0
     * 
     * @param string $email
     * 
     * @return integer
     */
    private function findEmail( $email )
    {
        $user = User::where('email', $email)->get();
        $isUserValid = ( count( $user ) == 1 );
        if ( $isUserValid ) {
            return $user[0]->id;
        } else {
            return 0;
        }
    }

     /**
     * 修改指定的 Player 資料
     * 傳入的 Request 應有以下資料：
     *   - (integer) trip_id
     *   - (integer) player_id
     *   - (string) name 
     *   - (string) desc (可為空)
     *   - (string) email (可為空)
     *   - (string) phone (可為空)
     * email 若已存在，則不可修改
     * 若該 email 帳戶已存在，則應自動連結
     * 
     * @return view
     */
    public function updatePlayer( Request $request )
    {
        $trip_id = $request->trip_id;

        $player_id = $request->player_id;
        $name = $request->name;
        $desc = $request->desc;
        $email = $request->email;
        $phone = $request->phone;

        $player = Players::find( $player_id );
        $player->name = $name;
        $player->description = $desc;
        $player->phone = $phone;

        $isEmailEmpty = ( $player->email == null );
        if( $isEmailEmpty ) {
            $player->email = $email;
        }

        $player->save();

        return $this->index( $trip_id );
    }

     /**
     * 刪除指定的 Player 資料
     * 傳入的 Request 應有以下資料：
     *   - (integer) trip_id
     *   - (integer) player_id
     * 
     * @return view
     */
    public function deletePlayer( Request $request )
    {
        
        $trip_id = $request->trip_id;

        $player_id = $request->player_id;

        $player = Players::find( $player_id );
        $player_id = $player->id;
        $player_participate = TripParticipates::where('participate_id', $player_id)->get()[0];

        $player->delete();
        $player_participate->delete();

        return $this->index( $trip_id );
    }
    
    /**
     * 當遇上非法輸入時，執行此程式
     * 
     * @return view
     */
    public function invalidRequest()
    {
        return view('welcome', ['status' => 'Request Invalid']);
    }

}
