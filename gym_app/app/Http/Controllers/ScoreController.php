<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreRequest;
use App\Models\Day;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ScoreController extends Controller
{

    public function show($day_name, ScoreRequest $request){
        $user = Auth::user();
        $data = $request->validated();

        if(Cache::has('user:'.$user->id.':date:'.$data['date'])){
            $score = Cache::get('user:'.$user->id.':date:'.$data['date']);
        }else{
            $day = Day::where([
                'user_id' => $user->id,
                'name' => $day_name
            ])->first();

            $score = Score::where([
                'day_id' => $day->id,
                'created_at' => $data['date']
            ])->first();

            Cache::put('user:'.$user->id.':date:'.$data['date'], $score, now()->addMinutes(1));
        }


        if($score){
            return response($score, 200);
        }

        return response([], 200);
    }

    public function store($day_name, ScoreRequest $request){
        // Podmiana lub dodanie do cache
        $user = Auth::user();
        $data = $request->validated();

        // check if given date reflects given day name
        $nameOfDay = date('l', strtotime($data['date']));
        if($nameOfDay !== $day_name){
            return response(['error' => 'Date doesn\'t match given day name'], 400);
        }

        $day = Day::where([
            'user_id' => $user->id,
            'name' => $day_name
        ])->first();

        $dayScore = 0;
        foreach($day->exercises as $exercise){
            $dayScore += floatval($exercise->weight)/10 * floatval($exercise->reps) * floatval($exercise->sets);
        }

        $score = Score::firstOrCreate([
            'day_id' => $day->id,
            'created_at' => $data['date']
        ]);

        $score->score = $dayScore;
        $score->save();

        Cache::put('user:'.$user->id.':date:'.$data['date'], $score, now()->addMinutes(1));

        return response($score, 201);

    }
}
