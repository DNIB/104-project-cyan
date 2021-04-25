<?php

namespace Tests\Feature;

use App\Models\Players;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class PlayerTest extends TestCase
{
    use RefreshDatabase;

    private $URL = '/trip/viewPlayer';
    private $FINAL_ID = '12';

    /**
     * 測試 Player Controller 的運行
     *
     * @return void
     */
    public function testPlayer()
    {
        $this->seed();
        $this->user = User::find(1);

        $this->createPlayer();
        $this->updatePlayer();
        $this->deletePlayer();
    }

    /**
     * Test Create Action of Players
     * 
     * @return void
     */
    private function createPlayer()
    {
        $response = $this->actingAs( $this->user )->call(
            'POST', 
            $this->URL, 
            ['name' => 'Taylor']
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            'POST', 
            $this->URL, 
            [
                'name' => 'Taylor1',
                'trip_id' => '1',
                'email' => "1@123.com",
            ]
        );
        $response->assertStatus(302);

        $response = $this->actingAs( $this->user )->call(
            'POST', 
            $this->URL, 
            [
                'name' => 'Taylor2',
                'trip_id' => '1',
                'email' => "ctest@gmail.com",
            ]
        );
        $response->assertStatus(302);

        $response = $this->actingAs( $this->user )->call(
            'POST', 
            $this->URL, 
            [
                'name' => 'Taylor3',
                'trip_id' => '1',
            ]
        );
        $response->assertStatus(302);
    }

    /**
     * Test Update Action of Players
     * 
     * @return void
     */
    private function updatePlayer()
    {
        $response = $this->actingAs( $this->user )->call(
            'PUT', 
            $this->URL, 
            [
                'player_id' => "12",
                'description' => 'Taylor',
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            'PUT', 
            $this->URL, 
            [
                'player_id' => "12",
                'name' => 'Taylor100',
            ]
        );
        $response->assertStatus(302);

        $response = $this->actingAs( $this->user )->call(
            'PUT', 
            $this->URL, 
            [
                'player_id' => "12",
                'name' => 'Taylor999',
                'email' => 'Taylor999@123.com',
            ]
        );
        $response->assertStatus(302);
    }

    /**
     * Test Delete Action of Players
     * 
     * @return void
     */
    private function deletePlayer()
    {
        $response = $this->actingAs( $this->user )->call(
            'DELETE', 
            $this->URL, 
            [
                'player_id' => "13",
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            'DELETE', 
            $this->URL, 
            [
                'player_id' => "12",
            ]
        );
        $response->assertStatus(302);
    }
}
