<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ReadRequest;
use App\Models\Players;
use App\Models\TripParticipates;
use App\Models\Trips;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Object_;

use function PHPSTORM_META\map;

class TripPlayerController extends Controller
{
    use ReadRequest;

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
        $user_id = Auth::id();
        
        $trip_players = Players::where('trip_id', $trip_id);
        $isRequestInvalid = !($this->checkRequestValid($trip_players, $user_id));

        $trip_players = Players::where('trip_id', $trip_id);

        if ($isRequestInvalid) {
            abort(403);
        }

        $trip = Trips::find($trip_id);
        $ret = [
            'players' => $trip_players->get(),
            'trip' => $trip,
        ];

        return view('trip.player_info', $ret);
    }

    /**
     * 確認以下事項：
     *   - 該行程參加表是否存在
     *   - 該使用者 ID 是否可以存取該行程參加表
     * 若皆成立，回傳 true；反之回傳 false
     * 
     * @param Players $trip_players
     * @param integer $user_id
     * 
     * @return boolean
     */
    private function checkRequestValid( $trip_players, $user_id )
    {
        $isNotEmpty = count($trip_players->get()) > 0;

        if ($isNotEmpty ) {
            $target_player = $trip_players->where('user_id', $user_id)->get();

            return count($target_player) > 0;
        }
        return false;
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
        $datas = $this->getData(
            $request,
            [
                'trip_id',
                'name',
                'description',
                'email',
                'phone',
            ],
            [
                'trip_id',
                'name',
                'desc',
                'email',
                'phone',
            ],
        );

        $isNameInvalid = empty($datas['name']);
        $isTripIdInvalid = empty($datas['trip_id']);

        $isRequestInvalid =  $isNameInvalid || $isTripIdInvalid;

        if ($isRequestInvalid) {
            abort(400);
        }

        $player = new Players;
        $player->fill($datas);

        $email_checker = $this->checkEmail($datas['email']);
        $player->user_id = ($email_checker) ? : null;

        $player->save();

        return redirect()->back();
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
        $isEmailNotEmpty = !empty($email);
        if ($isEmailNotEmpty ) {
            return $this->findEmail($email);
        }
        return 0;
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
        $isUserValid = ( count($user) == 1 );
        if ($isUserValid ) {
            return $user[0]->id;
        }
        return 0;
    }

     /**
      * 修改指定的 Player 資料
      * 傳入的 Request 應有以下資料：
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
        $player_id = $request->player_id;
        $email = $request->email;

        $datas = $this->getData(
            $request,
            [
                'name',
                'description',
                'phone',
            ],
            [
                'name',
                'desc',
                'phone',
            ],
        );

        $isNameInvalid = empty($datas['name']);
        
        $player = Players::find($player_id);
        $isPlayerInvalid = !isset($player);

        $isRequestInvalid = $isNameInvalid || $isPlayerInvalid;

        if ($isRequestInvalid) {
            abort(400);
        }

        $player = Players::find($player_id);
        $player->fill($datas);

        $isEmailEmpty = ( $player->email == null );
        if($isEmailEmpty ) {
            $player->email = $email;
            $email_checker = $this->checkEmail($email);
            $player->user_id = ($email_checker) ? : null;
        }

        $player->save();

        return redirect()->back();
    }

     /**
      * 刪除指定的 Player 資料
      * 傳入的 Request 應有以下資料：
      *   - (integer) player_id
      * 
      * @return view
      */
    public function deletePlayer( Request $request )
    {
        $player_id = $request->player_id;
        $player = Players::find($player_id);

        $isRequestInvalid = !( isset($player) && (!$player->trip_creator) );

        if ($isRequestInvalid) {
            abort(400);
        }

        $player->delete();
        return redirect()->back();
    }
}
