<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twilio\Rest\Client as TwilioClient;

class ExpedientCode extends Model
{
    protected $guarded = [];
    protected $table = 'expedient_codes';

    public static function generate($status = 0, $exp_date = null)
    {

       
        return static::create([
                "code" => Str::random(8),
                "status" => $status,
                'exp_date' => $exp_date ?? Carbon::now()->addMonths(6)
            ]);
    }


    public function send(User $user)
    {
        $status = [
            'status' => 1,
            'message' => 'ok'
        ];


        if ($user->fullPhone) {

            $message = "Doctor Blue. Utiliza este código para poder tener acceso al expediente clínico. El Código es: " . $this->code;


            try {
                $client = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));



                $response = $client->messages->create(
                // the number you'd like to send the message to
                    $user->fullPhone,
                    array(
                // A Twilio phone number you purchased at twilio.com/console
                        'from' => config('services.twilio.from'), //env('TWILIO_FROM'),
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
            //     \Twilio::message($user->fullPhone, $message);
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

        try {
            \Mail::raw('El código del expediente es:'.$this->code, function ($message) {
                    $message
                    ->to('gpsmedica@gmail.com')
                    ->subject('El código de autorización del expediente');
                });
        } catch (TransportExceptionInterface $e) {  //Swift_RfcComplianceException
            \Log::error($e->getMessage());
        }

        return $status;

    }

       
    
}
