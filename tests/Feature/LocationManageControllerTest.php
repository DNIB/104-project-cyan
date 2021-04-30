<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocationManageControllerTest extends TestCase
{
    use RefreshDatabase;

    private $URL = "/location";

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testLocation()
    {
        $this->seed();
        $this->user = User::find(1);

        $this->createTest();
        $this->updateTest();
        $this->readTest();
        $this->deleteTest();
    }

    /**
     * Test Create Action of Locations
     * 
     * @return void
     */
    private function createTest()
    {
        $response = $this->actingAs( $this->user )->call(
            "POST",
            $this->URL,
            [
                "select_name" => '',
                "select_desc" => 'test_desc',
                "lat_submit" => '1.234567',
                "lng_submit" => '7.654321',
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            "POST",
            $this->URL,
            [
                "select_name" => 'test_trip',
                "select_desc" => 'test_desc',
                "lat_submit" => 'string',
                "lng_submit" => '7.654321',
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            "POST",
            $this->URL,
            [
                "select_name" => 'test_trip',
                "select_desc" => 'test_desc',
                "lat_submit" => '1.234567',
                "lng_submit" => 'string',
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            "POST",
            $this->URL,
            [
                "select_name" => 'test_trip',
                "select_desc" => 'test_desc',
                "lat_submit" => '1.234567',
                "lng_submit" => '7.654321',
            ]
        );
        $response->assertStatus(302);
    }

    /**
     * Test Update Action of Locations
     * 
     * @return void
     */
    private function updateTest()
    {
        $response = $this->actingAs( $this->user )->call(
            "PUT",
            $this->URL,
            [
                'location_id' => '999999',
                "select_name" => 'test_trip_update',
                "select_desc" => 'test_desc_update',
            ]
        );
        $response->assertStatus(403);
        
        $response = $this->actingAs( $this->user )->call(
            "PUT",
            $this->URL,
            [
                'location_id' => '1',
                "select_name" => '',
                "select_desc" => 'test_desc_update',
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( User::find(2) )->call(
            "PUT",
            $this->URL,
            [
                'location_id' => '1',
                "select_name" => 'test_trip_update',
                "select_desc" => 'test_desc_update',
            ]
        );
        $response->assertStatus(403);

        $response = $this->actingAs( $this->user )->call(
            "PUT",
            $this->URL,
            [
                'location_id' => '1',
                "select_name" => 'test_trip_update',
                "select_desc" => 'test_desc_update',
            ]
        );
        $response->assertStatus(302);
    }
    
    /**
     * Test Read Action of Locations
     * 
     * @return void
     */
    private function readTest()
    {
        $response = $this->get($this->URL.'/create');
        $response->assertStatus(200);

        $response = $this->get($this->URL.'/read');
        $response->assertStatus(200);

        $response = $this->get($this->URL.'/edit');
        $response->assertStatus(404);

        $response = $this->get($this->URL.'/none');
        $response->assertStatus(404);
    }

    /**
     * Test Delete Action of Locations
     * 
     * @return void
     */
    private function deleteTest()
    {
        $response = $this->actingAs( User::find(2) )->call(
            "DELETE",
            $this->URL,
            [
                "location_id" => '1',
            ]
        );
        $response->assertStatus(403);

        $response = $this->actingAs( $this->user )->call(
            "DELETE",
            $this->URL,
            [
                "location_id" => '1',
            ]
        );
        $response->assertStatus(302);
    }
}
