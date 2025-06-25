<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginAsController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
     
    }

    public function store()
    {
        if(!auth()->user()->hasRole('administrador')){
            abort(403);
        }
        if(auth()->user()->email != 'admin@admin.com'){
            abort(403);
        }
        
        Auth::logout();

        Auth::loginUsingId(request('user_id'));

        return redirect('/');
     
    }
}
