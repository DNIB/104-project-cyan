<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocationApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A feature test of .
     *
     * @return void
     */
    public function testApiCalling()
    {
        $this->seed();
        $user = User::find(1);
        $token = $user->api_token;

        $response = $this->get('/api/user/getLocation');
        $response->assertStatus(302);

        $response = $this->get('/api/user/getTripLocation/1');
        $response->assertStatus(302);

        $response = $this->get('/api/user/getLocation'.'?api_token='.$token);
        $response->assertStatus(200);

        $response = $this->get('/api/user/getTripLocation/1'.'?api_token='.$token);
        $response->assertStatus(200);
    }
}
