<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\User;

class TripGetTest extends TestCase
{
    use RefreshDatabase;
    
    // 測試用 url 
    private $URL = "/trip";
    private $LOCATION_URL = "/trip/location";
    private $PLAYER_URL = "/trip/viewPlayer";

    // 預期回應
    private $OK = 200;
    private $FOUND = 302;
    private $FORBIDDEN = 403;
    private $NOTFOUND = 404;

    /**
     * 測試 Get 請求
     *
     * @return void
     */
    public function testGet()
    {
        $this->seed();
        $this->user = User::find(1);
        $this->user_invalid = User::find(2);

        $this->testGets( $this->FOUND, $this->FOUND );

        $this->actingAs( $this->user );
        $this->testGets( $this->NOTFOUND, $this->OK );

        $this->actingAs( $this->user_invalid );
        $response = $this->get( $this->PLAYER_URL.'/1' );
        $response->assertStatus( $this->FORBIDDEN );
    }

    private function testGets( $expect, $expect_diff )
    {
        $response = $this->get( $this->URL );
        $response->assertStatus( $expect );

        $response = $this->get( $this->PLAYER_URL );
        $response->assertStatus( $expect );

        $response = $this->get( $this->LOCATION_URL );
        $response->assertStatus( $expect );

        $response = $this->get( $this->PLAYER_URL.'/1' );
        $response->assertStatus( $expect_diff );

        $response = $this->get( $this->URL.'/index' );
        $response->assertStatus( $expect_diff );

        $response = $this->get( $this->URL.'/tripMap/1' );
        $response->assertStatus( $expect_diff );
    }
}
