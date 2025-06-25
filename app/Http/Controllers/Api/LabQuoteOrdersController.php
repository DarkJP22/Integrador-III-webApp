<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreatePatient;
use App\Actions\CreateUser;
use App\Http\Controllers\Controller;
use App\Jobs\SendAppPhoneMessageJob;
use App\Notifications\NewQuote;
use App\Notifications\QuoteRequestNotification;
use App\Office;
use App\Patient;
use App\Product;
use App\QuoteOrder;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LabQuoteOrdersController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function store(Request $request, CreateUser $createUser)
    {
        $data = $this->validate(request(), [
            'tipo_identificacion' => ['sometimes'],
            'ide' => ['required'],
            'name' => ['required'],
            'phone_number' => ['required'],
            'photos.*' => ['required', 'image', 'max:2048'], // 2MB Max
            'office_id' => ['required', 'exists:offices,id'],
        ]);

        return DB::transaction(function () use ($data, $createUser) {

            if (!$user = User::where('ide', $data['ide'])->first()) {

                $user = $createUser([
                    ...$data,
                    'name' => $data['name'],
                    'password' => $data['phone_number'],
                    'phone_country_code' => '+506',
                ], Role::whereName('paciente')->first());
            }
            if ($user && $user->ide) {

                if ($patient = Patient::where('ide', $data['ide'])->first()) {
                    if(!$user->patients()->where('patients.id', $patient->id)->exists()){
                        $user->patients()->save($patient, ['authorization' => 1]);
                    }
                } else {
                    $patient = app(CreatePatient::class)([
                        ...$data,
                        'first_name' => $data['name'],
                        'phone_country_code' => '+506',
                        'city' => '',
                    ]);
                    $user->patients()->save($patient, ['authorization' => 1]);
                }
            }

            $quoteOrder = QuoteOrder::create([
                ...Arr::except($data, ['photos', 'tipo_identificacion']),
                'patient_id' => $patient->id,
                'user_id' => $user->id
            ]);

            $quoteOrder->updatePhoto($data['photos'][0]); //->addPhotos($request->file('photos'));

            //$quoteOrder->user->notify(new NewQuote($quoteOrder));
            $lab = Office::find($quoteOrder->office_id);

            if($lab && $lab->whatsapp_number){
                $message = "El paciente ". Optional($quoteOrder->patient)->fullname." ha solicitado una cotización de boleta. Revisala en la pestaña de Cotizaciones de boletas";
                SendAppPhoneMessageJob::dispatch($message, $lab->fullWhatsappPhone)->afterCommit();
            }

            $lab->administrator()?->notify((new QuoteRequestNotification($quoteOrder))->afterCommit());



            return $quoteOrder;
        });
    }
}
