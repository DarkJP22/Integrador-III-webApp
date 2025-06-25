<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Twilio\Rest\Client as TwilioClient;

class PatientInvitation extends Model
{
    protected $guarded = [];
    protected $table = 'patient_invitations';

    public $timestamps = false;

    public static function generateFor(Patient $patient)
    {

        return static::create([
            "patient_id" => $patient->id,
            "code" => rand(0, 9999)
        ]);
    }

    public function send($phoneNumber = null)
    {
        $status = [
            'status' => 1,
            'message' => 'ok'
        ];

        $officeOrPharmacy = 'Doctor Blue';

        if ( auth()->user()->isAssistant() ) {

            $assistantUser = \DB::table('assistants_users')->where('assistant_id', auth()->user()->id)->first();


            if( auth()->user()->isClinicAssistant($assistantUser->user_id) ){
                $adminClinica = User::find($assistantUser->user_id);

                $officeOrPharmacy = $adminClinica->offices->first()->name;
                
               
            }
        }

        if ( auth()->user()->isClinic() ) {
         
            $officeOrPharmacy = auth()->user()->offices->first()->name;
                
        }

        if ( auth()->user()->isPharmacy() ) {

            $adminPharmacy = auth()->user();

            $officeOrPharmacy = $adminPharmacy->pharmacies->first()->name;
        
           
        }



        if ($phoneNumber) {

            $message = $officeOrPharmacy ." creó un acceso para que puedas observar el registro de Presiones / Glicemias del señor " . $this->patient->fullname . ". Acceder aqui para registrarse en la plataforma y ver la información del paciente. ".config('app.url')."/invitation/".$this->patient_id. "/register/". $this->code;

            \Log::info($message);


            try {
                $client = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));



                $response = $client->messages->create(
                // the number you'd like to send the message to
                    $phoneNumber,
                    array(
                // A Twilio phone number you purchased at twilio.com/console
                        'from' => config('services.twilio.from'),
                // the body of the text message you'd like to send
                        'body' => $message
                    )
                );

                \Log::info($response);
                

            } catch (\Services_Twilio_RestException $e) {
                $status['status'] = 0;
                $status['message'] = $e->getMessage();
                \Log::error($e->getMessage());

            } catch (\Twilio\Exceptions\RestException $e) {
                $status['status'] = 0;
                $status['message'] = $e->getMessage();
                \Log::error($e->getMessage());
            }
            // try {
            //     \Twilio::message($phoneNumber, $message);
            // } catch (\Services_Twilio_RestException $e) {
            //     $status['status'] = 0;
            //     $status['message'] = $e->getMessage();
            //     \Log::error($e->getMessage());

            // } catch (\Twilio\Exceptions\RestException $e) {
            //     $status['status'] = 0;
            //     $status['message'] = $e->getMessage();
            //     \Log::error($e->getMessage());
            // }

        }

        // if ($this->patient->email) {
        //     try {
        //         \Mail::to($this->patient->email)->send(new PatientInvitationEmail($this->code));
        //     } catch (TransportExceptionInterface $e) {  //Swift_RfcComplianceException
        //         \Log::error($e->getMessage());
        //     }
        // }

        return $status;

    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
