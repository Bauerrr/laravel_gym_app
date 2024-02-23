<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExerciseStoreRequest;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ExerciseController extends Controller
{
    public function store(ExerciseStoreRequest $request){
        $data = $request->validated();

        $user = Auth::user();
        $reps = $data['reps'] ?? 0;
        $sets = $data['sets'] ?? 0;
        $weight = $data['weight'] ?? 0;

        $exercises = Exercise::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'reps' => $reps,
            'sets' => $sets,
            'weight' => $weight
        ])->get();

        Cache::put('user:'.$user->id.':exercises', $exercises, now()->addMinutes(1));


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
            return response($exercise, 200);
        }

        return response(['error' => 'Exercise not found'], 404);

    }

    public function index(){
        $user = Auth::user();
        if(Cache::has('user:'.$user->id.':exercises')){
            $exercises = Cache::get('user:'.$user->id.':exercises');
        }else{
            $exercises = Exercise::where([
                'user_id' => $user->id
            ])->get();

            Cache::put('user:'.$user->id.':exercises', $exercises, now()->addMinutes(1));
        }

        return response($exercises, 200);

    }

    public function destroy($id){
        $user = Auth::user();
        $exercise = Exercise::where([
            'user_id' => $user->id,
            'id' => $id
        ])->first();

        if($exercise){
            $exercise->delete();
            if(Cache::has('user:'.$user->id.':exercises')){
                $exercises = Cache::get('user:'.$user->id.':exercises');
                $exercises = $exercises->except($exercise->id);
                Cache::put('user:'.$user->id.':exercises', $exercises, now()->addMinutes(1));
            }

            return response(['message' => 'Exercise '.$id.' deleted successfully'], 200);
        }

        return response(['error' => 'Exercise not found'], 404);

    }

    public function update($id, Request $request){
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

            if(Cache::has('user:'.$user->id.':exercises')){
                Cache::forget('user:'.$user->id.':exercises');
            }

            if($exercise->days()){
                foreach($exercise->days as $day){
                    if(Cache::has('user:'.$user->id.':day:'.$day->name.':exercises')){
                        Cache::forget('user:'.$user->id.':day:'.$day->name.':exercises');
                    }
                }
            }

            return response(['message' => 'Exercise '.$id.' updated successfully'], 200);
        }

        return response(['error' => 'Exercise not found'], 404);

    }

}
