<?php

namespace App\Http\Controllers;


use App\Http\Requests\PatientRequest;
use App\Repositories\PatientRepository;
use Illuminate\Validation\Rule;
use App\Patient;
use App\PatientCode;
use App\User;
use App\Repositories\MedicRepository;
use App\PatientInvitation;
use App\EmergencyContact;
use Illuminate\Support\Facades\Validator;


class UserPatientsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PatientRepository $patientRepo, MedicRepository $medicRepo)
    {
        $this->middleware('auth');
        $this->patientRepo = $patientRepo;
        $this->medicRepo = $medicRepo;
       
    }

    public function index()
    {
        $search['q'] = request('q');
        
        
        $patients = auth()->user()->patients()->search($search);
       

        $patients = $patients->with('emergencyContacts')->latest()->paginate(10);

        if (request()->wantsJson()) {
            return response($patients, 200);
        }

        return view('patients.index', compact('patients', 'search'));
    }

    /**
     * Guardar paciente
     */
    public function create()
    {
        $user = auth()->user();

        
        if( $patient = Patient::where('ide', $user->ide)->first() ){
            $user->patients()->save($patient, ['authorization' => 1]);

            return redirect('/');
        }

        return view('patients.register')->with('user');
    }

    /**
     * Guardar paciente
     */
    public function store(PatientRequest $request)
    {

        $v = Validator::make(request()->all(), [
            'tipo_identificacion' => 'nullable',
            'ide' => 'nullable',

        ]);

        $v->sometimes('tipo_identificacion', 'required', function ($input) {
            return $input->ide != '';
        });

        $v->sometimes('ide', 'digits:9', function ($input) {
            return $input->tipo_identificacion == '01';
        });

        $v->sometimes('ide', 'digits:10', function ($input) {
            return $input->tipo_identificacion == '02' || $input->tipo_identificacion == '04';
        });

        $v->sometimes('ide', 'digits_between:11,12', function ($input) {
            return $input->tipo_identificacion == '03';
        });

        $v->validate();
        
        $data = $request->all();
        $data['authorization'] = 1;

        $accountToAssign = null;

       
        
        $medic = null;
        if(isset($data['medic_to_assign']) && $data['medic_to_assign'] ){
    
            $medic = User::find($data['medic_to_assign']);
            $patient = $this->patientRepo->store($data, $medic);

        }else{
            $patient = $this->patientRepo->store($data);
        }

        if(request('account')){
            
            $accountToAssign = User::where('email', request('account'))->first();

            if(!$accountToAssign->patients()->where('patients.id', $patient->id)->exists()){
            
                $accountToAssign->patients()->attach($patient);
            }
           
        }

       

        if (auth()->user()->isAssistant()) {
            //$boss_assistant = \DB::table('assistants_users')->where('assistant_id', auth()->id())->first();

            //$boss = User::find($boss_assistant->user_id);
            //$boss->patients()->save($patient, ['authorization' => $data['authorization']]); // se agrega al administrador de la clincia
         

                $office = auth()->user()->clinicsAssistants->first();

                if(!$office->patients()->where('patients.id', $patient->id)->exists()){
                    $office->patients()->save($patient, ['authorization' => 1]);
                }

                $medics = $this->medicRepo->findAllByOfficeWithoutPaginate($office->id);

                foreach($medics as $medicClinic){ //se agregan a todos los medicos de la clinica
                    if( !$medic || ($medic->id != $medicClinic->id) ){

                        if(!$medicClinic->patients()->where('patients.id', $patient->id)->exists()){
                            $medicClinic->patients()->save($patient, ['authorization' => $data['authorization']]);
                        }
                    }
                }
        }
        if (auth()->user()->isClinic()) {
         
            //auth()->user()->patients()->save($patient, ['authorization' => $data['authorization']]); // se agrega al administrador de la clincia
            $office = auth()->user()->offices->first();

            if(!$office->patients()->where('patients.id', $patient->id)->exists()){
                $office->patients()->save($patient, ['authorization' => 1]);
            }

            $medics = $this->medicRepo->findAllByOfficeWithoutPaginate($office->id);

            foreach($medics as $medicClinic){ //se agregan a todos los medicos de la clinica
                if( !$medic || ($medic->id != $medicClinic->id) ){
                    $medicClinic->patients()->save($patient, ['authorization' => $data['authorization']]);
                }
            }
       }
        // if (!auth()->user()->isPatient()) {
        //     $user_patient = $this->patientRepo->createUser($patient, $data);
          
        // }else{
        //     if( ($patient->email && auth()->user()->email != $patient->email) || ($patient->ide && $patient->ide != auth()->user()->ide) ){
        //         $user_patient = $this->patientRepo->createUser($patient, $data);
        //     }
        // }

        if (request('invitation')) {

            $code = PatientInvitation::generateFor($patient);

            $status = $code->send('+506' . request('invitation'));

        }
        if (request('contact_name') && request('contact_phone_number')) {
            
            $contact = EmergencyContact::create([
                'name' => request('contact_name'),
                'patient_id' => $patient->id,
                'phone_country_code' => request('contact_phone_country_code'),
                'phone_number' => request('contact_phone_number'),

            ]);

        }

        if ($request->expectsJson()) {
            return response()->json($patient->load('emergencyContacts'), 201);
        }

        return Redirect('/');
    }

    /**
     * Actualizar Paciente
     */
    public function update($id)
    {
        
        $v = Validator::make(request()->all(), [
            'tipo_identificacion' => 'nullable',
            'ide' => ['nullable', Rule::unique('patients')->ignore($id)],
            'first_name' => 'required',
            'last_name' => 'nullable',
            'gender' => 'required',
            'phone_country_code' => 'required',
            'birth_date' => 'required',
            'province' => 'required',
            'phone_number' => ['required', 'digits_between:8,15'],//, Rule::unique('patients')->ignore($id)],
            'email' => ['nullable','email']//, Rule::unique('patients')->ignore($id)]//'required|email|max:255|unique:patients',

        ]);

        $v->sometimes('tipo_identificacion', 'required', function ($input) {
            return $input->ide != '';
        });

        $v->sometimes('ide', 'digits:9', function ($input) {
            return $input->tipo_identificacion == '01';
        });

        $v->sometimes('ide', 'digits:10', function ($input) {
            return $input->tipo_identificacion == '02' || $input->tipo_identificacion == '04';
        });

        $v->sometimes('ide', 'digits_between:11,12', function ($input) {
            return $input->tipo_identificacion == '03';
        });

        $v->validate();

        // $this->validate(request(), [ //se valida que no exista en user el correo q quiere cambiar
        //         'phone_number' => ['required', 'digits_between:8,15', Rule::unique('users')->ignore(auth()->id())],
        //         'email' => ['nullable','email', Rule::unique('users')->ignore(auth()->id())]
        // ]);

        $patient = $this->patientRepo->update($id, request()->all());

        return $patient->load('emergencyContacts');
    }

    public function destroy(Patient $patient)
    {
        $patient = $this->patientRepo->delete($patient->id);


         if (request()->wantsJson()) {

            if($patient === true){

                return response([], 204);
            }

            return response(['message'=>'No se puede eliminar paciente por que tiene citas asignadas'], 422);

        }

        return Redirect('/');
    }

    public function generateAuth(Patient $patient)
    {
       
        $code = PatientCode::generateFor($patient);

        $status = request('contacts') ? $code->sendMultiple(request('contacts')) : $code->send();

        

        if (request()->expectsJson()) {
            if ($status['status'] == 1) {
              
                return response()->json($code, 201);

            } else {
              
                return response()->json(['message' => 'El codigo se ha creado pero ha ocurrido un error al por sms. ' . $status['message'], ], 500);
            }

          
        
        
        }

        if ($status['status'] == 1) {
            flash('Se ha enviado un codigo al teléfono para poder utilizarlo en la autorización de usos de expediente clínico!', 'success');

        } else {
            flash('El codigo se ha creado pero ha ocurrido un error al por sms. ' . $status['message'], 'danger');
        }

        

        return back();
    }

    /**
     * Mostrar lista de pacientes de un doctor
     */
    public function responsables(Patient $patient)
    {
        $responsables = [];

        $users = $patient->user()->whereHas('roles', function ($q) {
            $q->where('name', 'paciente');

            })->get();

       
        
        foreach ($users as $user) {
            
            if ($user->phone_number != $patient->phone_number){

                $contact = [
                    'name' => $user->name,
                    'phone_country_code' => $user->phone_country_code,
                    'phone_number' => $user->phone_number
                ];

                $responsables[] = $contact;

            }
            

          
        }

        foreach ($patient->emergencyContacts as $user) {
            $contact = [
                'name' => $user->name,
                'phone_country_code' => $user->phone_country_code,
                'phone_number' => $user->phone_number
            ];

            $responsables[] = $contact;
        }


        if (request()->wantsJson()) {
            return response($responsables, 200);
        }

      
    }

    
}
