<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Validation\Rule;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;



class ProfileController extends Controller
{
    public function __construct(UserRepository $userRepo) {
        $this->middleware('auth');
        
        $this->userRepo = $userRepo;
    }

    public function show()
    {

       $user = auth()->user();
       
       $this->authorize('isAdmin', $user);

       $tab = request('tab');

    
       return view('admin.profile', [
           'profileUser' => $user
         
           
       ]);
    }

    /**
     * Actualizar informacion basica del medico
     */
    public function update(User $user)
    {
        $this->authorize('isAdmin', $user);

        $this->validate(request(), [
            'name' => 'required',
            'phone_country_code' => 'required',
            'phone_number' => ['required', 'digits_between:8,15', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
          
        ]);

        $user = $this->userRepo->update($user->id, request()->all());

        flash('Cuenta Actualizada', 'success');

        return Redirect('admin/profiles');

    }


}
