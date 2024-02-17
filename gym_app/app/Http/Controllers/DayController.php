<?php

namespace App\Http\Controllers;

use App\Http\Requests\DayRequest;
use App\Models\Day;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DayController extends Controller
{

    public function show($name){
        // Dodanie cache na exercises dla danego dnia
        $user = Auth::user();
        $day = Day::where([
            'user_id' => $user->id,
            'name' => $name
        ])->first();

        return response($day->exercises, 200);
    }

    public function addExercise($name, DayRequest $request){
        // Przerobienie cache z poprzedniej funkcji żeby zawierało nowe dane
        $user = Auth::user();
        $data = $request->validated();

        $day = Day::where([
            'user_id' => $user->id,
            'name' => $name
        ])->first();

        $exercise = Exercise::where([
            'user_id' => $user->id,
            'id' => $data['exercise_id']
        ])->first();

        if($exercise){
            $day->exercises()->attach($exercise->id);

            return response(['message' => 'Exercise '.$exercise->name.' added to '.$day->name], 200);
        }

        return response(['error' => 'Exercise '.$exercise->id.' not found'], 404);
    }

    public function deleteExercise($name, DayRequest $request){
        // Przerobienie cache z poprzedniej funkcji żeby zawierało nowe dane
        $user = Auth::user();
        $data = $request->validated();
        $day = Day::where([
            'user_id' => $user->id,
            'name' => $name
        ])->first();

        $exercise = Exercise::where([
            'user_id' => $user->id,
            'id' => $data['exercise_id']
        ])->first();

        if($exercise){
            $day->exercises()->detach($exercise->id);

            return response(['message' => 'Exercise '.$exercise->name.' detached from '.$day->name], 200);
        }

        return response(['error' => 'Exercise '.$exercise->id.' not found'], 404);
    }


}
