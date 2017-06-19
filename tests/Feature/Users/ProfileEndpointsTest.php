<?php

namespace Tests\Feature\Users;

use Tests\TestCase;
use App\Entities\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfileEndpointsTest extends TestCase
{

    use DatabaseMigrations;

    function setUp()
    {
        parent::setUp();
        $this->installApp();
    }

    function test_it_gets_user_profile()
    {
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/me');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => 'Jose Fonseca',
                'email' => 'jose@example.com',
                'roles' => []
            ]
        ]);
    }

    function test_it_can_update_logged_user_profile_all_entity()
    {
        Passport::actingAs(User::first());
        $response = $this->json('PUT', '/api/me', [
            'name' => 'Jose Fonseca Edited',
            'email' => 'jose@example.com'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca Edited',
            'email' => 'jose@example.com',
        ]);
        $response->assertJson([
            'data' => [
                'name' => 'Jose Fonseca Edited',
                'email' => 'jose@example.com',
                'roles' => []
            ]
        ]);
    }

    function test_it_can_update_profile_partial_entity()
    {
        Passport::actingAs(User::first());
        $response = $this->json('PATCH', '/api/me', [
            'name' => 'Jose Fonseca Edited'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca Edited',
            'email' => 'jose@example.com',
        ]);
        $response->assertJson([
            'data' => [
                'name' => 'Jose Fonseca Edited',
                'email' => 'jose@example.com',
                'roles' => []
            ]
        ]);
    }

    function test_it_validates_input_for_update_profile()
    {
        Passport::actingAs(User::first());
        $response = $this->json('PATCH', '/api/me', [
            'name' => ''
        ]);
        $response->assertStatus(422);
    }

    function test_it_validates_input_for_email_on_update_profile()
    {
        Passport::actingAs(User::first());
        $user = factory(User::class)->create();
        $response = $this->json('PATCH', '/api/me', [
            'email' => $user->email
        ]);
        $response->assertStatus(422);
    }

}
