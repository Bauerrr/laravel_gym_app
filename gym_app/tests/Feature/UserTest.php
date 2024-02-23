<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{

    public function test_user_signup_valid_credentials(): void
    {
        $response = $this->postJson('/api/signup', [
            'name' => 'FTestUser3',
            'email' => 'FTestUser3@test.com',
            'password' => 'FTestUser3!',
            'password_confirmation' => 'FTestUser3!'
        ]);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->hasAll(['user', 'token'])
            );

    }

    public function test_user_login_valid_credentials(): string
    {
        $response = $this->postJson('/api/login', [
            'email' => 'FTestUser3@test.com',
            'password' => 'FTestUser3!',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['user', 'token'])
            );

        $responseData = json_decode($response->getContent());

        return $responseData->token;
    }

    /**
     * @depends  test_user_login_valid_credentials
     */
    public function test_user_logout($token): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/logout');

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('success', true)
            );
    }

    public function test_user_signup_already_exists(): void
    {
        $response = $this->postJson('/api/signup', [
            'name' => 'FTestUser3',
            'email' => 'FTestUser3@test.com',
            'password' => 'FTestUser3!',
            'password_confirmation' => 'FTestUser3!'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('success', false)
                ->etc()
            );

    }

    public function test_delete_user(): void
    {
        $response = User::where(['email'=>'FTestUser3@test.com'])->first()->delete();
        $this->assertTrue($response);
    }

    public function test_user_signup_invalid_credentials(): void
    {
        $response = $this->postJson('/api/signup', [
            'name' => 'FTestUser3',
            'email' => 'FTestUser',
            'password' => 'FTestUser3!',
            'password_confirmation' => 'FTestUser3!'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('success', false)
                ->etc()
            );
    }

    public function test_user_login_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'FTestUser3@test.com',
            'password' => 'FTestUser3!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('error', 'The Provided credentials are not correct')
            );
    }

}
