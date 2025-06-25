<?php
namespace App\Actions;

use App\Patient;
use App\User;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Http\Response;

class ShareLinkAppMobileAction {

    public function execute(Patient $patient, $fullphone, $message = null) : Response
    {
        $status = [
            'status' => 1,
            'message' => 'ok'
        ];

        $entity = $this->getEntity();


        if($fullphone){
            
            if( !$message ){

                $linkAndroid = (getUrlAppPacientesAndroid()) ? "Android = ".getUrlAppPacientesAndroid() : "";
                $linkIos = (getUrlAppPacientesIos()) ? "ios: ". getUrlAppPacientesIos() : "";
                $linkVideo1 = "https://youtu.be/06yTUkdYinE";
                $linkVideo2 = "https://youtu.be/pvOETG56OLM";

                $message = "¿Presión? ¿Alergias? ¿Medicamentos? {$entity} le invita a consultar su historial de atención desde su teléfono. Doctor Blue {$linkAndroid} {$linkIos}.";
            }
          
           

                try {
                    $client = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));



                    $response = $client->messages->create(
                    // the number you'd like to send the message to
                        $fullphone,
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
        }

        if (request()->wantsJson()) {

            if($status['status'] == 1){
              
                   return response($status['message'], 200);
           }else{
              
               return response($status['message'], 500);
           }
        
           
       }

       return response($status['message'], 200);
    }

    private function getEntity(){

        $entity = 'Doctor Blue';

        if ( auth()->user()->isAssistant() ) {

            $assistantUser = \DB::table('assistants_users')->where('assistant_id', auth()->user()->id)->first();


            if( auth()->user()->isClinicAssistant($assistantUser->user_id) ){
                $adminClinica = User::find($assistantUser->user_id);

                $entity = $adminClinica->offices->first()->name;
                
               
            }
        }

        if ( auth()->user()->isClinic() ) {
         
            $entity = auth()->user()->offices->first()->name;
                
        }

        if ( auth()->user()->isPharmacy() ) {

            $adminPharmacy = auth()->user();

            $entity = $adminPharmacy->pharmacies->first()->name;
        
           
        }

        return $entity;
    }
}