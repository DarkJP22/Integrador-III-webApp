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

    $affiliationUsers = AffiliationUsers::where('active', "Pending")->get();

    return view('admin.affiliation.index', compact('affiliationUsers'));
}
//Se hizo una modificación para que se pueda ver las solicitudes de afiliación por estado y nombre Grpo G1
public function showToState(Request $request)
{
    $estado = $request->input('estado');
    $nombre = $request->input('nombre');

    $query = AffiliationUsers::query();

    // Filtro por estado
    if (in_array($estado, ['Pending', 'Approved', 'Denied'])) {
        $query->where('active', $estado);
    }

    // Filtro por nombre (relación con user)
    if (!empty($nombre)) {
        $query->whereHas('user', function ($q) use ($nombre) {
            $q->where('name', 'like', "%{$nombre}%");
        });
    }

    $affiliationUsers = $query->get();

    return view('admin.affiliation.index', compact('affiliationUsers'));
}
//Fin de la modificación para que se pueda ver las solicitudes de afiliación por estado y nombre Grpo G1
public function active($id)
{
    $requestaffiliation = AffiliationUsers::find($id);

    if ($requestaffiliation) {

        $requestaffiliation->active = "Approved";
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
public function inactive($id)
{
    $requestaffiliation = AffiliationUsers::find($id);

    if ($requestaffiliation) {
        $requestaffiliation->active = "Denied";
        $requestaffiliation->save();
    }
    return back();
}
}

    