<?php

namespace App\Http\Controllers\Api\Medic;


use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;

class SocialProfileController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');

    }

    public function update()
    {
        $user = request()->user();

        $data = $this->validate(request(), [
            'schools' => ['required', 'string', 'max:255'],
            'titles' => ['required', 'string', 'max:255'],
            'services' => ['required', 'string', 'max:255'],
        ]);


        $user->social_profile_fields = $data;
        $user->save();


        return $user;


    }



}
