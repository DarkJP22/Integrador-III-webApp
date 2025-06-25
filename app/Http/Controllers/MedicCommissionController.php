<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;




class MedicCommissionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');


    }

    public function update(User $medic)
    {

        $this->validate(request(), [
            'commission' => 'required|numeric',
        ]);

        $medic->update([
            'commission' => request('commission')
        ]);

        if (request()->wantsJson()) {


            return response([], 200);


        }
       

        return back();
    }
}
