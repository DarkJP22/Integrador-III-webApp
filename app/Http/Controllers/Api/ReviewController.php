<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ReviewApp;

class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Lista de todas las citas de un doctor sin paginar
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'app' => 'required',
            'comment' => ['required'],
            'rating' => 'required',

        ]);

        $user = $request->user();

        $review = new ReviewApp;
        $review->storeReview($user->id, request('comment'), request('rating'), request('app'));


        return $review;
    }
}
