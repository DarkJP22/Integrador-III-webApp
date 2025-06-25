<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\RequestOffice;
use Edujugon\PushNotification\PushNotification;
use App\Notifications\ClinicIntegrated;
use GuzzleHttp\Client;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class OfficeRequestController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
        $this->client = new Client([
            'timeout' => 60,
        ]);

       
    }
    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        $this->authorize('create', RequestOffice::class);

        $search['status'] = request('status');

        if (isset($search['status']) && $search['status'] != "") {
            $requestOffices = RequestOffice::with('user')->where('status', '=', $search['status'])->orderBy('created_at', 'DESC')->paginate(10);
        } else {

            $requestOffices = RequestOffice::with('user')->orderBy('created_at', 'DESC')->paginate(10);
        }


        return view('admin.clinics.offices.index', compact('requestOffices', 'search'));

    }


    /**
     * Active a user.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function active($id)
    {
        $requestOffice = RequestOffice::find($id);

        if ($requestOffice) {

            $requestOffice->status = 1;
            $requestOffice->save();

            if ($requestOffice->user->push_token) {

                $url = config('services.onesignal.url_api');
                $headers = [
                    'Authorization' => 'Basic ' . config('services.onesignal.api_key'), //env('API_KEY_ONESIGNAL_MEDICS'),
                    'Content-Type' => 'application/json'
                ];
                $content = 'Ya puedes agregar la clinica ' . $requestOffice->name . ' a tu perfil para programar y recibir citas';
                $title = 'La integracion de la clinica ha sido realizada';
                $data = [
                    'app_id' => config('services.onesignal.app_id'),
                    'include_player_ids' => array(
                        $requestOffice->user->push_token
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
                        'type' => 'appointment',
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
                    //return \GuzzleHttp\Psr7\Message::toString($e->getResponse());
                    \Log::info('Error Mensaje Push OneSignal Http: ' . \GuzzleHttp\Psr7\Message::toString($e->getResponse()));
    
    
                }
               
            }



            try {

                $requestOffice->user->notify(new ClinicIntegrated($requestOffice));


            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
            {
                \Log::error($e->getMessage());
            }    
            

           




        }


        return back();
    }

    /**
     * Inactive a user.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function inactive($id)
    {
        $requestOffice = \DB::table('request_offices')
            ->where('id', $id)
            ->update(['status' => 0]);

        return back();
    }

    /**
     * Eliminar consulta(cita)
     */
    public function destroy($id)
    {
        $requestOffice = RequestOffice::find($id);

        $requestOffice = $requestOffice->delete();

        return back();
    }



}
