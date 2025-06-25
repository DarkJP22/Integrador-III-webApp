<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Validation\Rule;
use App\Repositories\UserRepository;

class ProfileController extends Controller
{
    public function __construct(UserRepository $userRepo) {
        $this->middleware('auth');
        
        $this->userRepo = $userRepo;
    }

    public function show(User $user)
    {
       $this->authorize('update', $user);

       return view('user.profile', [
           'profileUser' => $user
       ]);
    }

    /**
     * Actualizar informacion basica del medico
     */
    public function update(User $user)
    {
        $this->authorize('update', $user);
        
        $this->validate(request(), [
            'name' => 'required',
            'phone_country_code' => 'required',
            'phone_number' => ['required', 'digits_between:8,15'],//,Rule::unique('users')->ignore(auth()->id())],
            'email' => ['nullable','email', Rule::unique('users')->ignore(auth()->id())],
            'ide' => ['nullable', Rule::unique('users')->ignore(auth()->id())]
        ]);

        $user = $this->userRepo->update($user->id, request()->all());

        flash('Cuenta Actualizada', 'success');

        return Redirect('profiles/'.$user->id);

    }


}
