<?php

namespace App;

use App\Notifications\PatientCodeEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twilio\Rest\Client as TwilioClient;

class PatientCode extends Model
{
    protected $guarded = [];
    protected $table = 'patient_codes';
    public $timestamps = false;

    protected $casts = [
        'was_used' => 'boolean'
    ];

    public static function generateFor(Patient $patient)
    {

        return static::create([
            "patient_id" => $patient->id,
            "code" => rand(0, 9999)
        ]);
    }

    public function send()
    {
        $status = [
            'status' => 1,
            'message' => 'ok'
        ];


        if ($this->patient->fullPhone) {

            $message = "Doctor Blue. Utiliza este código para poder autorizar la vista del expediente clínico del paciente ". $this->patient->fullname.". El Código es: " . $this->code;


            try {
                $client = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));

                $response = $client->messages->create(
                // the number you'd like to send the message to
                    $this->patient->fullPhone,
                    array(
                // A Twilio phone number you purchased at twilio.com/console
                        'from' => config('services.twilio.from'),
                // the body of the text message you'd like to send
                        'body' => $message
                    )
                );

                \Log::info($response);
                

            } catch (\Services_Twilio_RestException $e) {

                \Log::error($e->getMessage());

            } catch (\Twilio\Exceptions\RestException $e) {

                \Log::error($e->getMessage());
            }
            // try {
            //     \Twilio::message($this->patient->fullPhone, $message);
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

        // Se le envia al administrador de gps
        
        try {
            \Mail::raw('El código del paciente es:'.$this->code, function ($message) {
                    $message
                    ->to('gpsmedica@gmail.com')
                    ->subject('El código de autorización del paciente '.$this->patient->fullname);
                });
        } catch (TransportExceptionInterface $e) {  //Swift_RfcComplianceException
            \Log::error($e->getMessage());
        }
        

        return $status;

    }
    public function sendMultiple($contacts)
    {
        $status = [
            'status' => 1,
            'message' => 'ok'
        ];
        

        if ( $contacts ) {

            $message = "Doctor Blue. Utiliza este código para poder autorizar la vista del expediente clínico del paciente ". $this->patient->fullname.". El   Código es: " . $this->code;


            try {
                foreach ( $contacts as $contact) {
                    $client = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));

                    $response = $client->messages->create(
                    // the number you'd like to send the message to
                        $contact,
                        array(
                    // A Twilio phone number you purchased at twilio.com/console
                            'from' => config('services.twilio.from'),
                    // the body of the text message you'd like to send
                            'body' => $message
                        )
                    );
    
                    \Log::info($response);
                }
               
                

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
            //     foreach ( $contacts as $contact) {
            //         \Twilio::message($contact, $message);
            //     }
                
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

         // Se le envia al administrador de gps
         try {
            \Mail::raw('El código del paciente es:'.$this->code, function ($message) {
                    $message
                    ->to('gpsmedica@gmail.com')
                    ->subject('El código de autorización del paciente '.$this->patient->fullname);
                });
        } catch (TransportExceptionInterface $e) {  //Swift_RfcComplianceException
            \Log::error($e->getMessage());
        }
        


        return $status;

    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
