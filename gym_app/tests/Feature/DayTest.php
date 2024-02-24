<?php

namespace Tests\Feature;

use App\Models\Day;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DayTest extends TestCase
{
    private $token;
    private $user;
    private $exercise;
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::firstOrCreate([
            'name' => 'FETestUser1',
            'email' => 'FETestUser1@test.com',
            'password' => bcrypt('FETestUser1!')]);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach($days as $day){
            Day::firstOrCreate(
                ['user_id' => $this->user->id,
                'name' => $day]
            );
        }

        $this->token = $this->user->createToken('main')->plainTextToken;

        $this->exercise = Exercise::firstOrCreate([
            'user_id' => $this->user->id,
            'name' => 'Test Exercise 1',
            'reps' => 10,
            'sets' => 10,
            'weight' => 10]);

    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->user->tokens()->delete();
        $this->token = null;
        $this->user = null;
        $this->exercise = null;
    }

    public function test_attach_exercise_to_monday(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/days/Monday',[
                'exercise_id' => $this->exercise->id
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Exercise Test Exercise 1 added to Monday'
            ]);
    }

    public function test_attach_exercise_to_tuesday(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/days/Tuesday',[
                'exercise_id' => $this->exercise->id
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Exercise Test Exercise 1 added to Tuesday'
            ]);
    }

    /**
     * @depends  test_attach_exercise_to_monday
     */
    public function test_show_exercises_for_monday(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/days/Monday');

        $response
            ->assertStatus(200);
    }

    /**
     * @depends  test_attach_exercise_to_tuesday
     */
    public function test_show_exercises_for_tuesday(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/days/Tuesday');

        $response
            ->assertStatus(200);
    }

    /**
     * @depends  test_attach_exercise_to_monday
     */
    public function test_detach_exercise_from_monday(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->deleteJson('/api/days/Monday',[
                'exercise_id' => $this->exercise->id
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Exercise Test Exercise 1 detached from Monday'
            ]);
    }

    /**
     * @depends  test_attach_exercise_to_tuesday
     */
    public function test_detach_exercise_from_tuesday(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->deleteJson('/api/days/Tuesday',[
                'exercise_id' => $this->exercise->id
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Exercise Test Exercise 1 detached from Tuesday'
            ]);
    }


    public function test_detach_exercise_from_tuesday_bad_id(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->deleteJson('/api/days/Tuesday',[
                'exercise_id' => 1230123
            ]);

        $response
            ->assertStatus(404)
            ->assertJson([
                'error' => 'Exercise 1230123 not found'
            ]);
    }
}
