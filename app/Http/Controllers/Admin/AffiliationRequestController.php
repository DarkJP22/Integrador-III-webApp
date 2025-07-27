<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\RequestOffice;
use App\AffiliationUsers;
use Edujugon\PushNotification\PushNotification;
use App\Notifications\ClinicIntegrated;
use GuzzleHttp\Client;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AffiliationRequestController extends Controller
{
    
    //Obtener las solicitudes de afiliacion
    
   public function index()
{

    $affiliationUsers = AffiliationUsers::where('active', 0)->get();

    return view('admin.affiliation.index', compact('affiliationUsers'));
}


public function active($id)
{
    $requestaffiliation = AffiliationUsers::find($id);

    if ($requestaffiliation) {

        $requestaffiliation->active = 1;
        $requestaffiliation->save();
        //Esto se dejará documentado para futuras modificaciones grupo G1
/*
        if ($requestaffiliation->user->push_token) {

            $url = config('services.onesignal.url_api');
            $headers = [
                'Authorization' => 'Basic ' . config('services.onesignal.api_key'),
                'Content-Type' => 'application/json'
            ];

            
            $content = 'Tu solicitud de afiliación ha sido aprobada. Ahora puedes acceder a tu cuenta y comenzar a disfrutar los beneficios.';
            $title = '¡Afiliación aprobada con éxito!';

            $data = [
                'app_id' => config('services.onesignal.app_id'),
                'include_player_ids' => array(
                    $requestaffiliation->user->push_token
                ),
                'contents' => [
                    'en' => $content,
                    'es' => $content
                ],
                'headings' => [
                    'en' => $title,
                    'es' => $title
                ],
                'data' => [
                    'type' => 'affiliation',
                    'title' => $title,
                    'body' => $content,
                    'url' => '/notifications'
                ]
            ];

            try {
                $response = $this->client->request('POST', $url, ['headers' => $headers, 'json' => $data]);
                $body = $response->getBody();
                $content = $body->getContents();
                $result = json_decode($content);

                \Log::info('Mensaje Push OneSignal Http: ' . json_encode($result));

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                \Log::info('Error Mensaje Push OneSignal Http: ' . \GuzzleHttp\Psr7\Message::toString($e->getResponse()));
            }
        }

        try {
            $requestaffiliation->user->notify(new ClinicIntegrated($requestaffiliation)); 
        } catch (TransportExceptionInterface $e) {
            \Log::error($e->getMessage());
        }
    }
*/
    }
    return back();
  
}


}

