<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\ResetCodeEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twilio\Rest\Client as TwilioClient;

class ResetCode extends Model
{
   
    protected $fillable =["user_id",'code', 'phone'];
    protected $table = 'reset_codes';

    public $timestamps = false;

   public static function generateFor(User $user)
   {

       return static::create([
           "user_id" => $user->id,
            "phone" => $user->fullPhone,
            "code" => rand(0, 9999)
       ]);
   }

   public function send()
   {
      $status = [
        'status' => 1,
        'message' => 'ok'
      ];
    
      if($this->user->fullPhone){

        $message = "Utiliza el codigo para poder cambiar la contraseña de tu usuario en Doctor Blue. El Código es: ". $this->code;


        try {
            $client = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));



            $response = $client->messages->create(
            // the number you'd like to send the message to
                $this->user->fullPhone,
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
        //     \Twilio::message($this->user->fullPhone, $message);
        // } catch ( \Services_Twilio_RestException $e ) {
        //      $status['status'] = 0;
        //      $status['message'] = $e->getMessage();
        //      \Log::error($e->getMessage());
        
        // } catch (\Twilio\Exceptions\RestException $e) {
        //     $status['status'] = 0;
        //     $status['message'] = $e->getMessage();
        //     \Log::error($e->getMessage());
        // }

      }
       
      if ($this->user->email) {
            try {

                $this->user->notify(new ResetCodeEmail($this->code));
                $status['status'] = 1;
            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
            {
                $status['status'] = 0;
                \Log::error($e->getMessage());
            }    
           
      }

      return $status;

   }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
