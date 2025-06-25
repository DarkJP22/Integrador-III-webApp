<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Office;
use App\Repositories\MedicRepository;
use Illuminate\Support\Facades\Notification;


class OfficeMedicController extends Controller
{

    public function __construct(MedicRepository $medicRepo)
    {
        $this->middleware('auth');
        $this->medicRepo = $medicRepo;

    }

    public function index(Office $office = null)
    {
       
        $search['q'] = request('q');
       
        if( !$office ){
            $office = auth()->user()->offices->first();
          
        }

        $medics = $this->medicRepo->findAllByOfficeWithoutPaginate($office->id);

        
        return $medics;
        

        
    }

    public function update(User $medic, Office $office)
    {
        
        if (!$medic->verifyOffice($office->id)) {
            $office = $medic->offices()->updateExistingPivot($office->id, ['verified' => 1]);
            
        }


        return back();
    }

    public function permissionFe(Office $office, User $medic)
    {
        $medic->offices()->updateExistingPivot($office->id, ['permission_fe' => 1]);

        return back();
    }

    public function noPermissionFe(Office $office, User $medic)
    {
        $medic->offices()->updateExistingPivot($office->id, ['permission_fe' => 0]);

        return back();
    }

    public function destroy(Office $office, User $medic)
    {

        
        $medic->offices()->detach($office);
  


        return back();
    }
}
