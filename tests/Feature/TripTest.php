<?php

namespace Tests\Feature;

use App\Models\TripLocations;
use App\Models\Trips;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TripTest extends TestCase
{
    use RefreshDatabase;

    private $URL = "/trip";

    /**
     * A feature test of Class Trips.
     * CRUD Test
     *
     * @return void
     */
    public function testTrip()
    {
        $this->seed();
        $this->user = User::find(1);

        $this->createTest();
        $this->last_trip_id = Trips::orderBy('id', 'desc')->get()[0]->id;
        
        $this->updateTest();

        $locations = TripLocations::get();
        $this->updateLocationOfTrip( $locations[0]->trip_order );
        $this->updateLocationOfTrip( $locations[1]->trip_order );

        $this->deleteTest( Trips::limit(1)->get()[0]->id );
    }

    private function createTest()
    {
        $response = $this->actingAs( $this->user )->call(
            "POST",
            $this->URL,
            [
                "trip_desc" => "Error",
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            "POST",
            $this->URL,
            [
                "trip_name" => "Name",
                "trip_desc" => "Has Name",
            ]
        );
        $response->assertStatus(302);
    }

    private function updateTest()
    {
        $response = $this->actingAs( $this->user )->call(
            "PUT",
            $this->URL,
            [
                'trip_id' => $this->last_trip_id + 1,
                "trip_name" => "Name+",
                "trip_desc" => "Has Name+",
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            "PUT",
            $this->URL,
            [
                'trip_id' => $this->last_trip_id,
                "trip_name" => "Name+",
                "trip_desc" => "Has Name+",
            ]
        );
        $response->assertStatus(302);
    }

    private function updateLocationOfTrip( $target_order )
    {
        $response = $this->actingAs( $this->user )->call(
            "PUT",
            "/trip/locationOrder",
            [
                'trip_id' => $this->last_trip_id,
                "location_order" =>$target_order,
                "change" => "bad",
            ]
        );
        $response->assertStatus(400);
        
        echo TripLocations::get('trip_order'), "\n";

        $response = $this->actingAs( $this->user )->call(
            "PUT",
            "/trip/locationOrder",
            [
                'trip_id' => $this->last_trip_id,
                "location_order" =>$target_order,
                "change" => "lower",
            ]
        );
        $response->assertStatus(302);

        echo TripLocations::get('trip_order'), "\n";

        $response = $this->actingAs( $this->user )->call(
            "PUT",
            "/trip/locationOrder",
            [
                'trip_id' => "1",
                "location_order" =>$target_order,
                "change" => "upper",
            ]
        );
        $response->assertStatus(302);
    }

    private function deleteTest( $id )
    {
        $response = $this->actingAs( $this->user )->call(
            "DELETE",
            $this->URL,
            [
                'trip_id' => 'notNumber',
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            "DELETE",
            $this->URL,
            [
                'trip_id' => '999999999999',
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->user )->call(
            "DELETE",
            $this->URL,
            [
                'trip_id' => $id,
            ]
        );
        $response->assertStatus(302);
    }
}
