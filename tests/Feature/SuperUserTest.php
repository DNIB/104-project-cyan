<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SuperUserTest extends TestCase
{
    use RefreshDatabase;

    private $URL = "/user";

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSuperUser()
    {
        $this->seed();
        $this->superuser = User::find(1);
        $this->normaluser = User::find(2);

        $this->readTest();
        $this->userTest();
        $this->locationTest();
        $this->playerTest();
        $this->tripTest();
        $this->errorTest();
    }

    /**
     * Test Read Action
     * 
     * @return void
     */
    private function readTest()
    {
        $this->actingAs( $this->normaluser );
        $response = $this->get($this->URL);
        $response->assertStatus(403);

        $response = $this->get($this->URL.'/location');
        $response->assertStatus(403);

        $response = $this->get($this->URL.'/player');
        $response->assertStatus(403);

        $response = $this->get($this->URL.'/trip');
        $response->assertStatus(403);

        $this->actingAs( $this->superuser );
        $response = $this->get($this->URL.'/location');
        $response->assertStatus(200);

        $response = $this->get($this->URL.'/player');
        $response->assertStatus(200);

        $response = $this->get($this->URL.'/trip');
        $response->assertStatus(200);
    }

    /**
     * Test Update and Delete of User
     * 
     * @return void
     */
    private function userTest()
    {
        $response = $this->actingAs( $this->superuser )->call(
            "PUT",
            $this->URL,
            [
                'id' => "999999",
                'name' => 'tname',
                'email' => 'test@123.com',
                "password" => '87654321',
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->superuser )->call(
            "PUT",
            $this->URL,
            [
                'id' => "2",
                'name' => 'tname',
                'email' => 'test@123.com',
                "password" => '87654321',
            ]
        );
        $response->assertStatus(302);

        $response = $this->actingAs( $this->superuser )->call(
            "DELETE",
            $this->URL,
            [
                'delete_id' => "string",
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->superuser )->call(
            "DELETE",
            $this->URL,
            [
                'delete_id' => "999999",
            ]
        );
        $response->assertStatus(400);

        $response = $this->actingAs( $this->superuser )->call(
            "DELETE",
            $this->URL,
            [
                'delete_id' => "2",
            ]
        );
        $response->assertStatus(302);
    }

    private function tripTest(){
        $response = $this->actingAs( $this->superuser )->call(
            "PUT",
            $this->URL.'/trip',
            [
                'id' => "1",
                'name' => 'test_name',
                'description' => 'test_description',
            ]
        );
        $response->assertStatus(302);

        $response = $this->actingAs( $this->superuser )->call(
            "DELETE",
            $this->URL.'/trip',
            [
                'id' => "1",
            ]
        );
        $response->assertStatus(302);
    }

    private function locationTest(){
        $response = $this->actingAs( $this->superuser )->call(
            "PUT",
            $this->URL.'/location',
            [
                'id' => "1",
                'name' => 'test_name',
                'description' => 'test_description',
                'lat' => '1.234567',
                'lng' => '7.654321',
            ]
        );
        $response->assertStatus(302);

        $response = $this->actingAs( $this->superuser )->call(
            "DELETE",
            $this->URL.'/location',
            [
                'id' => "1",
            ]
        );
        $response->assertStatus(302);
    }

    private function playerTest(){
        $response = $this->actingAs( $this->superuser )->call(
            "PUT",
            $this->URL.'/player',
            [
                'id' => "1",
                'name' => 'test_name',
                'description' => 'test_description',
                'user_id' => '1',
                'trip_id' => '1',
                'email' => 'test@test.com',
                'phone' => '0909',
            ]
        );
        $response->assertStatus(302);

        $response = $this->actingAs( $this->superuser )->call(
            "DELETE",
            $this->URL.'/player',
            [
                'id' => "1",
            ]
        );
        $response->assertStatus(302);
    }

    private function errorTest()
    {
        $response = $this->actingAs( $this->superuser )->call(
            "PUT",
            $this->URL.'/none',
            [
                'id' => "1",
            ]
        );
        $response->assertStatus(403);
    }
}
