<?php

namespace App\Http\Controllers\Medic;

use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use App\Office;
use App\Repositories\OfficeRepository;

class OfficeController extends Controller
{

    public function __construct(OfficeRepository $officeRepo) {
        $this->middleware('auth');
        
        $this->officeRepo = $officeRepo;
    }


    public function index()
    {
        $this->authorize('viewAny', Office::class);
        
        $search['q'] = request('q');

        $offices = $this->officeRepo->findAllByDoctor(auth()->user(), $search);
       

        if (request()->wantsJson()) {
            return response($offices, 200);
        }


        return view('medic.offices.index', compact('offices', 'search'));
    }

    public function create()
    {

        return view('medic.offices.create');
    }

    public function edit(Office $office)
    {
        
        return view('medic.offices.edit', compact('office'));
    }

   


}
