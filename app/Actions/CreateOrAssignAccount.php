<?php

namespace App\Actions;

use App\Patient;
use App\Repositories\PatientRepository;
use App\Repositories\UserRepository;
use App\Role;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateOrAssignAccount
{

    public function __construct(protected PatientRepository $patientRepo, protected UserRepository $userRepo)
    {
    }

    public function __invoke(array $data): Patient | null
    {

        return DB::transaction(function () use ($data) {
            
            $dataAccountPatient = [
                'first_name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone_country_code' => '+506',
                'phone_number' => $data['phone'],
                'ide' => $data['identificacion'],
                'tipo_identificacion' => $data['tipo_identificacion'] ?? '01',
                'gender' => 'm',
                'birth_date' => Carbon::now()->toDateString(),
                'province' => '5',


            ];

            $user_patient = null;



            if (auth()->user()->isPharmacy()) {

                $adminPharmacy = auth()->user();

                $pharmacy = $adminPharmacy->pharmacies->first();
            }


            $patient = Patient::where('ide', $data['identificacion'])->first();

            $user = User::where('ide', $data['identificacion'])->first();
            $user_patient = $patient;

            if ($patient && !$user) {


                $user_patient = $this->createUser($patient, $dataAccountPatient);

                //se desabilito por pedido de julio 19-10-2020
                // if($dataAccountPatient['phone_number']){
                //     $actionSMS->execute($patient, $dataAccountPatient['phone_country_code'].$dataAccountPatient['phone_number']);
                // }


                return $user_patient;
            }
           
            if (!$patient && !$user) {

                $v = Validator::make($dataAccountPatient, [
                    'tipo_identificacion' => 'nullable',
                    'ide' => 'nullable',
                    'phone_country_code' => 'required',
                    'phone_number' => 'required|digits_between:8,15',
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




                $dataAccountPatient['authorization'] = 1;
                $dataAccountPatient['complete_information'] = 0;

                $patient = $this->patientRepo->store($dataAccountPatient);

                if ($patient->email || $patient->ide) {

                    $user_patient = $this->createUser($patient, $dataAccountPatient);

                    //se desabilito por pedido de julio 19-10-2020
                    //$actionSMS->execute($patient, $user_patient->phone_country_code.$user_patient->phone_number);

                }

                if ($pharmacy) {
                    $pharmacy->patients()->save($patient, ['authorization' => $dataAccountPatient['authorization']]);

                    $pharmacredential = $pharmacy->pharmacredential;

                    if ($pharmacredential && !$patient->apipharmacredentials()->where('pharmacy_id', $pharmacy->id)->exists()) {

                        $patient->apipharmacredentials()->create([
                            "name" => $pharmacredential->name,
                            "api_url" => $pharmacredential->api_url,
                            "access_token" => $pharmacredential->access_token,
                            "pharmacy_id" => $pharmacredential->pharmacy_id
                        ]);
                    }
                }


                return $user_patient;
            }

            return $user_patient;
            # code...
        });
    }

    public function createUser($patient, $data)
    {
        $user_patient = null;
        //validamos que en users no hay email que va a registrase como paciente
        if ((isset($data['email']) && $data['email']) || (isset($data['ide']) && $data['ide'])) {

            $data['password'] = (isset($data['password']) && $data['password']) ? $data['password'] : $data['phone_number'];

            $data['name'] = $data['first_name'];
            $data['role'] = Role::whereName('paciente')->first();
            $data['api_token'] = Str::random(50);


            $user = $this->userRepo->store($data);
            $user_patient = $user->patients()->save($patient, ['authorization' => 1]);

            //se desabilito por pedido de julio 19-10-2020
            // try {

            //     $user->notify(new NewPatient($user));

            // } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
            // {
            //     \Log::error($e->getMessage());
            // }    


        }


        return $user_patient;
    }
}
