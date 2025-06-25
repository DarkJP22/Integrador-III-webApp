<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Patient;
use App\Http\Controllers\Controller;
class PatientAvatarController extends Controller
{
    /**
     * Store a new user avatar.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Patient $patient)
    {
        request()->validate([
            'avatar' => ['required', 'image', 'max:1000']
        ]);
        $patient->update([
            'avatar_path' => request()->file('avatar')->store('avatars', 's3')
        ]);
        return response([], 204);
    }
}
