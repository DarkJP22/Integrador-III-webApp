<?php

namespace App\Http\Controllers;

use App\Jobs\SendAppNotificationJob;
use App\Jobs\SendAppPhoneMessageJob;
use App\Patient;
use App\Notifications\AppInformation;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PatientMediaController extends Controller
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


    public function store()
    {
        $pharmacy = auth()->user()->pharmacies->first();

        // if(auth()->user()->isAssistant()){
        //     $clinic = auth()->user()->clinicsAssistants->first();
        // }else{
        //     $clinic = auth()->user()->offices->first();
        // }
        // $weekOfYear = Carbon::now()->weekOfYear;

        // if (auth()->user()->isPharmacy() && \DB::table('notifications_pharmacy_limit')->where('pharmacy_id', $pharmacy->id)->where('week', $weekOfYear)->count() >= 1)
        // {

        //     flash('Anuncio no enviado. Solo puedes enviar un anuncio por semana', 'danger');

        //     return back();

        // }else if ( (auth()->user()->isClinic() || auth()->user()->isAssistant()) && \DB::table('notifications_clinic_limit')->where('clinic_id', $clinic->id)->where('week', $weekOfYear)->count() >= 2) {

        //     flash('Anuncio no enviado. Solo puedes enviar 2 anuncios por semana', 'danger');

        //     return back();
        // }

        $emailsUsers = [];
        $tokensUsers = [];


        $video = request('video');
        $patientIds = request('patients');

        $title = $video['title'] ? $video['title'] : 'Nueva Información!!';
        $body = $video['description'] ? $video['description'] : 'Te ha llegado una nueva notificación de información de interés, revisala en el panel de notificaciones !!';


        $patients = Patient::with([
            'user' => function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'paciente');
                });
            }
        ])->whereIn('id', $patientIds)->get();

        foreach ($patients as $patient) {
            $user = $patient->user->first();

            if ($user) {

                if (auth()->user()->isPharmacy()) {

                    if ($user->push_token && $user->pharmacy_notifications) {
                        $tokensUsers[] = $user->push_token;
                    }
                }


                $notificationItem = [
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body,
                    'mediaType' => 'video',
                    'media' => $video['url']

                ];
                // $notificationsList[] = $notificationItem;


                //$user->appNotifications()->create($notificationItem);
                try {

                    $user->notify(new AppInformation($notificationItem));

                    if (auth()->user()->isPharmacy()) {

                        // \DB::table('notifications_pharmacy_limit')->insert(
                        //     ['pharmacy_id' => $pharmacy->id, 'user_id' => auth()->user()->id, 'week' => $weekOfYear,'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
                        // );
                    }


                } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                {
                    \Log::error($e->getMessage());
                }

                if ($user->phone_number) {

                    SendAppPhoneMessageJob::dispatch("Doctor Blue. Te ha llegado una nueva notificación de información de interés, revisala en el panel de notificaciones!!. https://mobile.cittacr.com",
                        $user->fullPhone)->afterCommit();
                }

            }
        }

        if (count($tokensUsers)) {

            $data = [
                'type' => 'media',
                'title' => $title,
                'body' => $body,
                'mediaType' => 'video',
                'media' => $video['url'],
                'url' => '/notifications'

            ];

            SendAppNotificationJob::dispatch($title, $body, $tokensUsers, $data)->afterCommit();
        }

        if (request()->wantsJson()) {
            return response([], 200);
        }

        flash('Anuncio enviado', 'success');

        return back();
    }


}
