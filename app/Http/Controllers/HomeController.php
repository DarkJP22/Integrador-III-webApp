<?php

namespace App\Http\Controllers;

use App\Setting;
use Carbon\Carbon;
use App\Notifications\NewContact;
use Illuminate\Support\Facades\Notification;
use App\User;
use App\Repositories\PatientRepository;
use App\Repositories\MedicRepository;
use App\Patient;
use App\Role;

class HomeController extends Controller
{
    protected $administrators;

    public function __construct(protected PatientRepository $patientRepo, protected MedicRepository $medicRepo)
    {
        //$this->middleware('auth');
        $this->administrators = User::whereHas('roles', function ($query) {
            $query->where('name', 'administrador');
        })->get();

    }


    public function index()
    {
        if (!auth()->check() || auth()->user()->hasRole('paciente')) {
            return view('user.home');
        }

        if (auth()->user()->hasRole('administrador')) {
            $settings = Setting::getAllSettings();

            return view('admin.home', compact('settings'));
        }

        if (auth()->user()->hasRole('operador')) {

            $medics = $this->medicRepo->findAll();
            $patients = Patient::paginate(10);

            return view('operator.home', compact('medics', 'patients'));
        }

        if(auth()->user()->disabled_by_payment){
            return view('pending-payment');
        }

        if (auth()->user()->hasRole('laboratorio')) {

            $medics = $this->medicRepo->findAll();
            $patients = Patient::paginate(10);
            $office = auth()->user()->offices->first();

            return view('lab.home', compact('medics', 'patients', 'office'));
        }


        if (auth()->user()->hasRole('clinica')) {

            if (!auth()->user()->offices->count()) {
                return Redirect('/office/register');
            }

            $office = auth()->user()->offices->first();

            $patients = $this->patientRepo->findAllOfClinic($office);

            return view('clinic.home', compact('patients', 'office'));
        }

        if (auth()->user()->hasRole('farmacia')) {

            if (!auth()->user()->pharmacies->count()) {
                return Redirect('/pharmacy/registerform');
            }

            $pharmacy = auth()->user()->pharmacies->first();

            return view('pharmacy.home', compact('pharmacy'));
        }

        if (auth()->user()->hasRole('asistente')) {

            $office = auth()->user()->clinicsAssistants->first();


            $patients = $this->patientRepo->findAllOfClinic($office);

            return view('assistant.home', compact('patients', 'office'));
        }

        if (!auth()->user()->current_role_id && auth()->user()->roles) {
            auth()->user()->update([
                'current_role_id' => auth()->user()->roles->first()?->id
            ]);
        }

        if (auth()->user()->isCurrentRole('esteticista')) {

            $appointments = [];
            return view('beautician.home', compact('appointments'));
        }

        if (auth()->user()->isCurrentRole('medico')) {


            if (!session()->has('office_id') || session('office_id') == '') {

                if (auth()->user()->offices->count()) {
                    session(['office_id' => auth()->user()->offices->first()->id]);
                }

            }


            return view('medic.home');
        }


    }

    public function support()
    {
        request()->validate([
            'subject' => 'required',
            'message' => 'required'

        ]);

        $dataMessage = request()->all();

        $dataMessage['user'] = auth()->user();

        Notification::send($this->administrators, new NewContact($dataMessage));

        return response([], 204);

    }

    public function tiposIdentificaciones()
    {
        $tipoIdentificaciones = [
            '01' => 'Cédula Física',
            '02' => 'Cédula Jurídica',
            '03' => 'DIMEX',
            '04' => 'NITE',
            '00' => 'No definido'


        ];

        return $tipoIdentificaciones;
    }
}
