<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExerciseTest extends TestCase
{
    private string $token = '19|xotUCOClXBuvayHkkQ0jm4Sp076nPbNNRhtMauQSf5369148';

    public function test_exercise_store_valid_information(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/exercises',[
            'name' => 'FTest Exercise 1',
            'reps' => 10,
            'sets' => 10,
            'weight' => 10
        ]);


        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'FTest Exercise 1 created successfully'
            ]);
    }

    public function test_exercise_store_only_name(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/exercises',[
            'name' => 'FTest Exercise 2',
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'FTest Exercise 2 created successfully'
            ]);
    }

    public function test_exercise_index(): array
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/exercises');

        $response->assertStatus(200);

        $responseData = json_decode($response->getContent());
        return [$responseData[0]->id, $responseData[1]->id];
    }

    /**
     * @depends  test_exercise_index
     */
    public function test_exercise_show_valid_id($id): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/exercises/'.$id[0]);

        $response->assertStatus(200);
    }

    public function test_exercise_show_invalid_id(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/exercises/404');

        $response->assertStatus(404);
    }

    /**
     * @depends  test_exercise_index
     */
    public function test_exercise_update_valid_information_patch($id): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->patchJson('/api/exercises/'.$id[0],[
                'reps' => 40
            ]);

        $response->assertStatus(200);
    }

    /**
     * @depends  test_exercise_index
     */
    public function test_exercise_update_valid_information_put($id): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->putJson('/api/exercises/'.$id[0],[
                'name' => 'FTest Exercise 1',
                'reps' => 10,
                'sets' => 10,
                'weight' => 10
            ]);

        $response->assertStatus(200);
    }


    public function test_exercise_update_invalid_id(): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->patchJson('/api/exercises/404',[
                'reps' => 40
            ]);

        $response->assertStatus(404);
    }

    /**
     * @depends  test_exercise_index
     */
    public function test_exercise_destroy_valid_id($id): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->deleteJson('/api/exercises/'.$id[0]);

        $response->assertStatus(200);
    }


    /**
     * @depends  test_exercise_index
     */
    public function test_exercise_destroy_valid_id2($id): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->deleteJson('/api/exercises/'.$id[1]);

        $response->assertStatus(200);
    }


    /**
     * @depends  test_exercise_index
     */
    public function test_exercise_destroy_invalid_id($id): void
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer '.$this->token)
            ->deleteJson('/api/exercises/'.$id[0]);

        $response->assertStatus(404);
    }

}
