<?php

namespace App\Http\Controllers;

use App\User;
use App\Patient;





class UserAuthorizationsController extends Controller
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

    public function index()
    {
        
        
        $patients = auth()->user()->patients()->with(['authorizations' => function ($q) {
            $q->whereHas('roles', function ($q) {
                $q->where('name', 'medico');
            });
        }]);


        $patients = $patients->paginate(10);

        if (request()->wantsJson()) {
            return response($patients, 200);
        }

      
    }

    public function destroy(Patient $patient, User $authorization)
    {
      

       $result = $patient->user()->updateExistingPivot($authorization->id, ['authorization'=> 0]);

         if (request()->wantsJson()) {

          

        return response([], 204);
         

          

        }

        return Redirect('/');
    }

 
}
