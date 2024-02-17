<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreRequest;
use App\Models\Day;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{

    public function show($day_name, ScoreRequest $request){
        // Cache na to (ważne, no chyba że się to frontem załatwi)
        $user = Auth::user();
        $data = $request->validated();

        $day = Day::where([
            'user_id' => $user->id,
            'name' => $day_name
        ])->first();

        $score = Score::where([
            'day_id' => $day->id,
            'created_at' => $data['date']
        ])->first();

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

        return response($score, 200);

    }
}
