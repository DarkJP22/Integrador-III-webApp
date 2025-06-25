<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\ReviewService;
use App\ReviewMedic;
use App\Http\Requests\PollRequest;

class PollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('send');

    }

    public function show(User $medic)
    {
    	
        return view('polls.show')->with(compact('medic'));
    }

    public function store($userId, PollRequest $request)
    {
        $user = User::find($userId);

        if ($user) {
            $reviewService = new ReviewService;
            $reviewService->storeReviewForUser($user->id, auth()->id(), request('comment1'), request('rating'));

            $reviewMedic = new ReviewMedic;
            $reviewMedic->storeReviewForUser($user->id, auth()->id(), request('comment2'), request('rating2'));

            flash('Encuesta enviada correctamente', 'success');
        }

       

        return Redirect('/');

    }
    public function send()
    {

        $exitCode = \Artisan::call('gps:sendPolls');

        return $exitCode;

    }

}
