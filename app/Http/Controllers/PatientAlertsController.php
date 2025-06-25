<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Patient;
use Illuminate\Validation\Rule;
use Edujugon\PushNotification\PushNotification;
use App\Notifications\AppInformation;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PatientAlertsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->client = new Client([
            'timeout' => 60,
        ]);

    }

   
    public function store(Patient $patient)
    {
        // return request('message');
        // dd(request('item'));

        $tokensUsers = [];
        $title = request('title') ? request('title') : 'Alerta Doctor Blue!!';
        $body = request('message') ? request('message') : 'Te ha llegado una nueva notificacion de informacion de interes, revisala en el panel de notificaciones !!';

        $usersPatient = $patient->user()->whereHas('roles', function ($q) {
            $q->where('name', 'paciente');
            })->get();

        foreach ($usersPatient as $user) {
            if($user->push_token){
                 $tokensUsers[] = $user->push_token;
            }
            $notificationItem = [
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
                'media' => '',
                'mediaType' => ''

            ];
        
            try {

                $user->notify(new AppInformation($notificationItem));

                
            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
            {
                \Log::error($e->getMessage());
            }  
        }

        if (count($tokensUsers)) {

            $url = config('services.onesignal.url_api');
            $headers = [
                'Authorization' => 'Basic ' . config('services.onesignal.api_key'),//env('API_KEY_ONESIGNAL_PATIENTS'),
                'Content-Type' => 'application/json'
            ];

            $body = [
                'app_id' => config('services.onesignal.app_id'),
                'include_player_ids' => $tokensUsers,
                'contents' => [
                    'en' => $body,
                    'es' => $body
                ],
                'headings' => [
                    'en' => $title,
                    'es' => $title
                ],
                'data' => [
                    'type' => 'alert',
                    'title' => $title,
                    'body' => $body,

                ]
            ];


            try {
                $response = $this->client->request('POST', $url, ['headers' => $headers, 'json' => $body]);
                $body = $response->getBody();
                $content = $body->getContents();
                $result = json_decode($content);

                \Log::info('Mensaje Push OneSignal Http: ' . json_encode($result));

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                //return \GuzzleHttp\Psr7\Message::toString($e->getResponse());
                \Log::info('Error Mensaje Push OneSignal Http: ' . \GuzzleHttp\Psr7\Message::toString($e->getResponse()));


            }
        }

        if (request()->wantsJson()) {
            return response([], 200);
        }

        flash('Alerta enviado', 'success');

        return back();
    }

    



}
