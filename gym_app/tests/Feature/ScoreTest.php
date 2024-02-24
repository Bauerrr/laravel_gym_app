<?php

namespace Tests\Feature;

use App\Models\Day;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ScoreTest extends TestCase
{
    private $token;
    private $user;
    private $monday;
    private $tuesday;
    private $exercise;
    private $exercise2;
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::where(['name' => 'FETestUser1'])->first();

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

        $this->exercise2 = Exercise::firstOrCreate([
            'user_id' => $this->user->id,
            'name' => 'Test Exercise 2',
            'reps' => 20,
            'sets' => 20,
            'weight' => 20]);

        $this->monday = Day::where([
            'user_id' => $this->user->id,
            'name' => 'Monday'
        ])->first();

        $this->tuesday = Day::where([
            'user_id' => $this->user->id,
            'name' => 'Tuesday'
        ])->first();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->user->tokens()->delete();
//        $this->token = null;
//        $this->user = null;
//        $this->exercise = null;
    }

    public function test_show_empty_score_for_monday(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/scores/Monday?date=2024-02-26');

        $response
            ->assertStatus(200);
    }

    /**
     * @depends  test_show_empty_score_for_monday
     */
    public function test_store_score_for_monday(): void
    {
        $this->monday->exercises()->attach($this->exercise);
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/scores/Monday',[
                'date' => '2024-02-26'
            ]);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('score', 100)
                ->etc()
            );
    }

    /**
     * @depends  test_store_score_for_monday
     */
    public function test_recalculate_score_for_monday(): void
    {
        $this->monday->exercises()->attach($this->exercise2);
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/scores/Monday',[
                'date' => '2024-02-26'
            ]);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('score', 900)
                ->etc()
            );
    }

    /**
     * @depends  test_recalculate_score_for_monday
     */
    public function test_show_score_for_monday(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/scores/Monday?date=2024-02-26');

        $this->monday->exercises()->detach($this->exercise);
        $this->monday->exercises()->detach($this->exercise2);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('score', 900)
                ->etc()
            );

    }
}
