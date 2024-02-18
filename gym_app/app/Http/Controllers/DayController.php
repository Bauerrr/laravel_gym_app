<?php

namespace App\Http\Controllers;

use App\Http\Requests\DayRequest;
use App\Models\Day;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DayController extends Controller
{

    public function show($name){
        $user = Auth::user();

        if(Cache::has('user:'.$user->id.':day:'.$name.':exercises')){
            $exercises = Cache::get('user:'.$user->id.':day:'.$name.':exercises');
        }else{
            $day = Day::where([
                'user_id' => $user->id,
                'name' => $name
            ])->first();

            $exercises = $day->exercises;

            Cache::put('user:'.$user->id.':day:'.$name.':exercises', $exercises, now()->addMinutes(1));

        }

        return response($exercises, 200);
    }

    public function addExercise($name, DayRequest $request){
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

            Cache::put('user:'.$user->id.':day:'.$name.':exercises', $day->exercises, now()->addMinutes(1));

            return response(['message' => 'Exercise '.$exercise->name.' added to '.$day->name], 200);
        }

        return response(['error' => 'Exercise '.$exercise->id.' not found'], 404);
    }

    public function deleteExercise($name, DayRequest $request){
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

            Cache::put('user:'.$user->id.':day:'.$name.':exercises', $day->exercises, now()->addMinutes(1));

            return response(['message' => 'Exercise '.$exercise->name.' detached from '.$day->name], 200);
        }

        return response(['error' => 'Exercise '.$exercise->id.' not found'], 404);
    }


}
