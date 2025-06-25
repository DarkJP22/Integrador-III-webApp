<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\PatientRepository;
use App\Patient;
use App\Repositories\AppointmentRepository;
use Illuminate\Validation\Rule;
use App\Http\Requests\PatientUserRequest;
use Illuminate\Validation\ValidationException;
use App\User;
class InvoicePatientsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PatientRepository $patientRepo, AppointmentRepository $appointmentRepo)
    {
        $this->middleware('auth');
        $this->patientRepo = $patientRepo;
        $this->appointmentRepo = $appointmentRepo;

    }

    /**
     * Mostrar lista de pacientes de un doctor
     */
    public function index()
    {
      
        $search['q'] = request('q');
        $office = auth()->user()->isAssistant() ? auth()->user()->clinicsAssistants->first() : auth()->user()->offices->first();
        $search['office_id'] = $office->id;
    
        //dd($search['office_id']);
        if(auth()->user()->isAssistant() || auth()->user()->isClinic() || auth()->user()->isLab()){

                $patients = Patient::search($search)
                  ->with(['accumulateds', 'discounts'])->latest()->paginate(10);
              
        }else{
            $patients = Patient::with(['accumulateds', 'discounts' => function ($query)  {
                   
                             $query->where('user_id', auth()->user()->id);
                        
                        }])
                        ->search($search)
                        ->latest()
                        ->paginate(10);
        }

        if (request()->wantsJson()) {
            return response($patients, 200);
        }

      
    }

}
