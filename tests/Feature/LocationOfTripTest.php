<?php

namespace Tests\Feature;

use App\Models\Locations;
use App\Models\TripLocations;
use App\Models\Trips;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocationOfTripTest extends TestCase
{
    use RefreshDatabase;

    private $URL = '/trip/location';

    /**
     * A feature test of Class Locations
     *
     * @return void
     */
    public function testExample()
    {
        $this->seed();
        $this->user = User::find(1);
        $this->trip = Trips::find(1);
        $this->location = Locations::find(1);
        $this->location_diff = Locations::find(2);

        $this->createTest();

        $last_location_id = count( $this->trip->locations()->get() );
        $last_order_id = TripLocations::find( $last_location_id )->trip_order;

        $this->updateTest( $last_order_id );
        $this->deleteTest( $last_order_id );
    }

    private function createTest()
    {
        $response = $this->actingAs( $this->user )->call(
            "POST",
            $this->URL,
            [
                "trip_id" => $this->trip->id,
                "location_id" => '999999',
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            "POST",
            $this->URL,
            [
                "trip_id" => '999999',
                "location_id" => $this->location->id,
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            "POST",
            $this->URL,
            [
                "trip_id" => $this->trip->id,
                "location_id" => $this->location->id,
            ]
        );
        $response->assertStatus(302);
    }

    private function updateTest( $order_id )
    {
        $response = $this->actingAs( $this->user )->call(
            "PUT",
            $this->URL,
            [
                "trip_id" => '999999',
                "location_id" => $this->location->id,
                'order_id' => $order_id,
            ]
        );
        $response->assertStatus(400);
        
        $response = $this->actingAs( $this->user )->call(
            "PUT",
            $this->URL,
            [
                "trip_id" => $this->trip->id,
                "location_id" => $this->location->id,
                'order_id' => $order_id,
            ]
        );
        $response->assertStatus(302);
    }

    private function deleteTest( $order_id )
    {
        $response = $this->actingAs( $this->user )->call(
            "DELETE",
            $this->URL,
            [
                "trip_id" => '999999',
                'order_id' => $order_id,
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            "DELETE",
            $this->URL,
            [
                "trip_id" => $this->trip->id,
                'order_id' => '999999',
            ]
        );
        $response->assertStatus(400);
        
        $response = $this->actingAs( $this->user )->call(
            "DELETE",
            $this->URL,
            [
                "trip_id" => $this->trip->id,
                'order_id' => $order_id,
            ]
        );
        $response->assertStatus(302);
    }
}
