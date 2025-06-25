<?php

namespace App\Http\Controllers;

use App\Role;

class UserRoleController extends Controller
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

    public function switch(Role $role)
    {
        if(!auth()->user()->hasRole($role))
        {
            flash('No se realizó el cambio de rol', 'success');
            return Redirect('/');
        }

        auth()->user()->update([
            'current_role_id' => $role->id
        ]);

        flash('Se realizó el cambio de role correctamente', 'success');
        
        return Redirect('/');
    }

   

    
}
