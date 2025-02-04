<?php

namespace Tests\Feature;

use App\Airport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AirportsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    /** @test */
    public function a_user_can_browse_airports()
    {
        $airport = Airport::latest()->first();

        $response = $this->get('/airports/');

        $response->assertStatus(200);

        $response->assertSee($airport);
    }
}
