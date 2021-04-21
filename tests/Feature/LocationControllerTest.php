<?php

namespace Tests\Feature;

use App\Http\Controllers\LocationManageController;
use App\Models\Locations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use Exception;
use Illuminate\Http\Request;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThan;
use function PHPUnit\Framework\assertLessThan;
use function PHPUnit\Framework\assertTrue;

class LocationControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試新增地點的頁面
     * 
     * @return void
     */
    public function testAddLocation()
    {
        $this->seed();

        $this->checkGuest();
        $this->checkAllIndex();

        $this->checkLocationCrud();

        $this->checkApiCall();
    }

    /**
     * 測試未登入者讀取所有頁面
     * 
     * @return void
     */
    private function checkGuest()
    {
        $urls = [
            '/location/create',
            '/location/read',
            '/location/edit',
        ];
        foreach ( $urls as $url ) {
            $this->checkGuestIndex( $url );
        }
    }

    /**
     * 測試未登入者指定 URL 的頁面
     * 
     * @param string $url
     * 
     * @return void
     */
    private function checkGuestIndex( $url )
    {
        $response = $this->get( $url );
        $response->assertStatus( 403 );
    }

    /**
     * 測試登入者讀取所有頁面
     * 
     * @return void
     */
    private function checkAllIndex()
    {
        $urls = [
            '/location/create',
            '/location/read',
            '/location/edit',
        ];
        foreach ( $urls as $url ) {
            $this->checkIndex( $url );
        }
        
        $url = '/location/error';
        $response = $this->get( $url );
        $response->assertStatus(404);
    }

    /**
     * 測試登入者讀取指定 URL 的頁面
     * 
     * @param string $url
     * 
     * @return void
     */
    private function checkIndex( $url )
    {
        $user = factory( User::class )->create();
        $response = $this->actingAs( $user )->get( $url );
        $response->assertStatus( 200 );
    }

    /**
     * 測試地點的 CRUD
     * 
     * @return void
     */
    private function checkLocationCrud()
    {
        $controller = new LocationManageController;
        
        $location_id = $this->checkLocationCreate( $controller );
        $this->checkLocationUpdate( $controller, $location_id );
        $this->checkLocationDelete( $controller, $location_id );

        $location_id = $this->checkLocationCreateWrong( $controller );
    }

    /**
     * 測試增加非法地點
     * 
     * @return integer
     */
    private function checkLocationCreateWrong( LocationManageController $controller )
    {
        $request = $this->produceRequest();
        $request->lat = "string";

        $url = '/location/create';

        $response = $this->post( $url );
        $response->assertStatus( 400 );
    }

    /**
     * 測試增加地點，並回傳增加地點的 id
     * 
     * @return integer
     */
    private function checkLocationCreate( LocationManageController $controller )
    {
        $request = $this->produceRequest();
        $controller->createLocation( $request );
        $id = $this->isRequestLocationExist( $request );
        assertGreaterThan( 0, $id );

        return $id;
    }

    /**
     * 測試修改該 id 地點的資料
     * 
     * @return void
     */
    private function checkLocationUpdate( LocationManageController $controller, $location_id )
    {
        $request = $this->produceRequest( 'update' );
        $request->location_id = $location_id;

        $controller->updateLocation( $request );
        $id = $this->isRequestLocationExist( $request, 'update' );

        assertGreaterThan( 0, $id );
    }

    /**
     * 測試刪除該 id 地點的資料
     * 
     * @return void
     */
    private function checkLocationDelete( LocationManageController $controller, $location_id )
    {
        $controller->deleteLocation( $location_id );
        $location = Locations::find( $location_id );
        $isLocationDelete = empty($location);
        
        assertTrue( $isLocationDelete );
    }

    /**
     * 產生 Create 及 Update 的 Request
     * 
     * @return Request $request
     */
    private function produceRequest( $action = 'create' )
    {
        $name = str_random(20);
        $desc = str_random(20);
        
        $request = new Request;
        $request->select_name = $name;
        $request->select_desc = $desc;

        if ( $action === 'create' ) {
            $lat = random_int(0, 180) / 1000000;
            $lng = random_int(0, 180) / 1000000;
            $request->lat_submit = $lat;
            $request->lng_submit = $lng;
        }

        return $request;
    }

    /**
     * 確認是否有符合 request 的地點
     * 
     * @param Request $request
     * 
     * @return boolean
     */
    private function isRequestLocationExist( Request $request, $action = 'create' )
    {
        $target = Locations::where('name', $request->select_name)
            ->where('description', $request->select_desc);
        
        if ( $action === 'create' ) {
            $target = $target->where('lat', $request->lat_submit)
                ->where('lng', $request->lng_submit);
        }

        $target = $target->get();
        if ( count( $target ) > 0 ) {
            return $target[0]->id;
        } else {
            return -1;
        }
    }

    /**
     * 確認 API 呼叫
     * 
     * @return void
     */
    private function checkApiCall()
    {
        $url = '/api/user/getLocation/1';
        $response = $this->get( $url );
        try {
            foreach ( $response as $result ) {
                $count = count( $result->original );
                break;
            }
            $EXPECT = 6;
            assertEquals( $count, $EXPECT );
        } catch (Exception $e) {
            assertTrue( false );
        }
        
    }
}
