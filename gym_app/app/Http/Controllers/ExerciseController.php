<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExerciseStoreRequest;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExerciseController extends Controller
{
    public function store(ExerciseStoreRequest $request){
        // Dodanie do cache z index nowego ćwiczenia
        $data = $request->validated();

        $user = Auth::user();
        $reps = $data['reps'] ?? 0;
        $sets = $data['sets'] ?? 0;
        $weight = $data['weight'] ?? 0;

        Exercise::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'reps' => $reps,
            'sets' => $sets,
            'weight' => $weight
        ]);

        return response([
            'message' => $data['name'] . " created successfully"
        ], 201);
    }

    public function show($id){
        $user = Auth::user();
        $exercise = Exercise::where([
            'user_id' => $user->id,
            'id' => $id
        ])->first();

        if($exercise){
            return response([$exercise], 200);
        }

        return response(['error' => 'Exercise not found'], 404);

    }

    public function index(){
        // Dodanie cache na exercises
        $user = Auth::user();
        $exercises = Exercise::where([
            'user_id' => $user->id
        ])->get();

        return response([$exercises], 200);

    }

    public function destroy($id){
        // Przerobienie cache z index żeby nie zawierał usuniętych ćwiczeń
        $user = Auth::user();
        $affectedRows = Exercise::where([
            'user_id' => $user->id,
            'id' => $id
        ])->delete();

        if($affectedRows){
            return response(['message' => 'Exercise '.$id.' deleted successfully'], 200);
        }

        return response(['error' => 'Exercise not found'], 404);

    }

    public function update($id, Request $request){
        // Przerobienie cache z index żeby nie zawierał starych informacji
        $user = Auth::user();
        $exercise = Exercise::where([
            'user_id' => $user->id,
            'id' => $id
        ])->first();

        if($exercise){
            $exercise->sets = $request->sets ?? $exercise->sets;
            $exercise->name = $request->name ?? $exercise->name;
            $exercise->reps = $request->reps ?? $exercise->reps;
            $exercise->weight = $request->weight ?? $exercise->weight;
            $exercise->save();

            return response(['message' => 'Exercise '.$id.' updated successfully'], 200);
        }

        return response(['error' => 'Exercise not found'], 404);

    }

}
