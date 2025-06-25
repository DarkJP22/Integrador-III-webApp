<?php

namespace App\Http\Controllers\Pharmacy;

use App\Actions\RegisterPatientFromPharmacy;
use App\Actions\ShareLinkAppMobileAction;
use App\Http\Controllers\Controller;
use App\Repositories\PatientRepository;
use App\Patient;


class PatientController extends Controller
{
    public function __construct(PatientRepository $patientRepo)
    {

        $this->patientRepo = $patientRepo;


        $this->middleware('auth');


    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        if (!auth()->user()->hasRole('farmacia')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['province'] = request('province');
        $search['conditions'] = request('conditions');


        $pharmacy = auth()->user()->pharmacies->first();

        if (request('ofPharmacy')) {
            $patients = $pharmacy->patients()->Search($search)->paginate();
        } else {

            $patients = $this->patientRepo->findAllOfPharmacy($pharmacy, $search);
        }

        if (request()->wantsJson()) {
            return response($patients, 200);
        }


        return view('pharmacy.patients.index', compact('patients', 'pharmacy', 'search'));

    }

    public function store(RegisterPatientFromPharmacy $registerPatientFromPharmacy)
    {

        $patient = $registerPatientFromPharmacy([
            ...request()->all(),
            'complete_information' => 0
        ]);

        if (request()->wantsJson()) {
            return response($patient, 201);
        }

        return redirect('/pharmacy/patients');
    }

    public function edit($id)
    {
        $patient = $this->patientRepo->findById($id);
        $pharmacy = auth()->user()->pharmacies->first();
        $tab = request('tab');

        $this->authorize('update', $patient);


        $pressures = $patient->pressures()->orderBy('created_at', 'DESC')->get();
        $sugars = $patient->sugars()->orderBy('created_at', 'DESC')->get();
        $medicines = $patient->medicines()->orderBy('created_at', 'DESC')->paginate(5);
        $pmedicines = $patient->pmedicines()->where('pharmacy_id', $pharmacy->id)->orderBy('created_at',
            'DESC')->paginate(5);
        $allergies = $patient->history->allergies->load('user.roles');


        //$files = $patient->archivos;

        return view('pharmacy.patients.edit',
            compact('patient', 'pressures', 'sugars', 'medicines', 'pmedicines', 'allergies', 'tab'));
    }

    public function shareApp(Patient $patient, ShareLinkAppMobileAction $action)
    {
        $patientsNumbers = request('contacts');

        foreach ($patientsNumbers as $number) {
            $action->execute($patient, $number);
        }


    }


}
