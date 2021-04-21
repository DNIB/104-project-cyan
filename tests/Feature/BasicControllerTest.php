<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class BasicControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試首頁
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * 測試 Home
     *
     * @return void
     */
    public function testHome()
    {
        $URL = '/home';

        $response = $this->get( $URL );
        $response->assertStatus(302);

        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get( $URL );
        $response->assertStatus(200);
    }
}
