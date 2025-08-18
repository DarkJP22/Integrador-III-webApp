<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrAssignAccount;
use App\Actions\ShareLinkAppMobileAction;
use App\AffiliationUsers;
use App\Discount;
use App\Jobs\SendAppPhoneMessageJob;
use App\Repositories\PatientRepository;
use App\Patient;
use App\Services\FarmaciaService;
use App\Setting;
use App\User;
use Illuminate\Support\Carbon;

class GeneralPatientsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(private PatientRepository $patientRepo, private FarmaciaService $farmaciaService)
    {
        $this->middleware('auth');
       
    }

    public function index()
    {
        $search['q'] = request('q');
        $patients = $this->patientRepo->listForClinics($search, 50);


        if (request()->wantsJson()) {
            return response($patients, 200);
        }

        return view('patients.index', compact('patients', 'search'));
    }

    public function verifyIsPatient($medic, $patient)
    {

        $patient = $this->patientRepo->findById($patient);

        $result = [
            'isPatient' => $patient->isPatientOf($medic),
            'isAuthorized' => $patient->isPatientAuthorizedOf($medic),
        ];

        return $result; //($patient->isPatientOf($medic)) ? 'yes' : 'no';
    }

    public function verifyIsPatientOfClinic($office, $patient)
    {

        $patient = $this->patientRepo->findById($patient);

        $result = [
            'isPatient' => $patient->isPatientOfClinic($office),
            'isAuthorized' => $patient->isPatientOfClinicAuthorizedOf($office),
        ];

        return $result; //($patient->isPatientOf($medic)) ? 'yes' : 'no';
    }

    public function historialCompras(Patient $patient)
    {
        $pharmacyId = request('pharmacy') ? request('pharmacy') : null;
        $start =  request('start') ? request('start') : Carbon::now()->subMonths(3)->format('Y-m-d');
        $end =  request('end') ? request('end') : Carbon::now()->format('Y-m-d');

      
        
        $historialCompras = $this->farmaciaService->getHistorialCompras($patient, $pharmacyId, $start, $end);
        

        return paginate($historialCompras, 10);
    }

    public function verifyIsPatientGpsMedica($ide)
    {

        $patient = Patient::where('ide', $ide)->first();
        
        $to = auth()->user()->isPharmacy() ? 'farmacia' : 'clinica';
//Se inicia con la modificación del grupo G1 

   $idAffiliation = $patient->user()->first()->id;
$affiliation = $idAffiliation
    ? AffiliationUsers::where('user_id', $idAffiliation)
                      ->where('active', "Approved")
                      ->first()
    : null;
$acceptedDiscountAffiliation = auth()->user()->accept_affiliates ;
//Fin de la modificación del grupo G1
        $result = [
            'patient' => $patient,
            'isPatient' => $patient ? true : false,
            'patientHasAccount' => $patient ? $patient->user()->whereHas('roles', function($q){
                $q->where('name', 'paciente');
            })->count() : false,
            'patientAccounts' => $patient ? $patient->user : [],
            'discount' => Discount::where('for_gps_users', 1)
                                    ->where('to', $to)->get()->first(),
            'lab_exam_discount' => Setting::getSetting('lab_exam_discount') ?? 0,
            'affiliation' => $affiliation, // Modificación del grupo G1
            'acceptedDiscountAffiliation' => $acceptedDiscountAffiliation, // Modificación del grupo G1
        ];

        return $result;
    }

    public function shareAppLink()
    {
        $data = request()->validate([
            'phone_number' => 'required',
            'message' => 'required',
        ]);
        
        SendAppPhoneMessageJob::dispatch($data['message'], $data['phone_number'])->afterCommit();
        
    }

    
}
