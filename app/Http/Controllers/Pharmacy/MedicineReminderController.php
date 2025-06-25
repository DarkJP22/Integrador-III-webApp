<?php

namespace App\Http\Controllers\Pharmacy;

use App\Enums\MedicineReminderStatus;
use App\Enums\MedicineReminderStatusNotification;
use App\Jobs\SendAppNotificationJob;
use App\Jobs\SendAppPhoneMessageJob;
use App\Medicine;
use App\Http\Controllers\Controller;
use App\MedicineReminder;
use App\Patient;
use App\Notifications\AppInformation;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MedicineReminderController extends Controller
{

    private $models = [
        MedicineReminder::class,
        Medicine::class
    ];

    public function __construct(protected Client $client)
    {
        $this->client = new Client([
            'timeout' => 60,
        ]);
    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        if (auth() && !auth()->user()?->hasRole('farmacia')) {
            return redirect('/');
        }

//        $search['q'] = request('q');
//        $search['start'] = request('start');
//        $search['end'] = request('end');
//
//
//        $pharmacy = auth()->user()->pharmacies->first();
//
//        $reminders = MedicineReminder::search($search)->with('patients')->paginate(10);
        //dd(request()->all());
        $search['q'] = request('q');
        $search['start'] = request('start') ?? now()->toDateString();
        $search['end'] = request('end');
        $search['status'] = request('status') ?? MedicineReminderStatus::NO_CONTACTED->value;

        $pharmacy = auth()->user()->pharmacies->first();

        $reminders = [];
       // dd($search);
        if($search['status'] && $search['status'] != -1)
        {
            $this->models = [MedicineReminder::class];
        }

        foreach ($this->models as $model) {
            $query = $model::query();
            //$fields = $model::$searchable;
            if($model === Medicine::class)
            {
                $query->where('creator_id', $pharmacy?->id);
                $query->where('creator_type', get_class($pharmacy));
            }else{
                $query->where('pharmacy_id', $pharmacy?->id);
            }

            $query->search($search);
//            foreach ($fields as $field) {
//                $query->orWhere($field, 'LIKE', '%'.$search['q'].'%');
//            }

            $results = $query->take(10)->get();

            foreach ($results as $result) {
                $parsedData = $result; //$result->only($fields);
                $parsedData['patients'] = $result->patients ?? collect([$result->patient]);
                $parsedData['generated'] = get_class($result) == MedicineReminder::class ? true : false;
                $reminders[] = $parsedData;
            }
        }

        $reminders = paginate($reminders, 20);
        $medicineReminderStatuses = MedicineReminderStatus::options();


        return view('pharmacy.medicinesreminder.index', compact('reminders', 'pharmacy', 'search', 'medicineReminderStatuses'));

    }

    public function send(MedicineReminder $reminder)
    {
        $emailsUsers = [];
        $tokensUsers = [];
        $title = request('title') ? request('title') : 'Recordatorio de medicamento!!';
        $body = request('body') ? request('body') : 'Tenemos nuevas unidades de tu medicamento ('.$reminder->name.') !!';

        $mimes = ['jpg', 'jpeg', 'bmp', 'png'];
        $fileUploaded = '';
        $patients = $reminder->patients->pluck('id');
        if (request()->file('file')) {
            $file = request()->file('file');
            $name = $file->getClientOriginalName();
            $ext = $file->guessClientExtension();
            $onlyName = Str::slug(pathinfo($name)['filename'], '-');

            if (in_array($ext, $mimes)) {
                $fileUploaded = $file->storeAs('marketing/'.auth()->id(), uniqid().'.'.$ext, 's3');
            }
        }

        $patients = Patient::with([
            'user' => function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'paciente');
                });
            }
        ])->whereIn('id', $patients)->get();

        foreach ($patients as $patient) {
            $user = $patient->user->first();

            if ($user && $user->push_token) {
                $tokensUsers[] = $user->push_token;

                $notificationItem = [
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body,
                    'media' => $fileUploaded ? \Storage::disk('s3')->url($fileUploaded) : ''
                ];
                // $notificationsList[] = $notificationItem;


                //$user->appNotifications()->create($notificationItem);
                try {

                    $user->notify(new AppInformation($notificationItem));
                } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                {
                    \Log::error($e->getMessage());
                }
            }

            if ($user?->phone_number) {

                SendAppPhoneMessageJob::dispatch($body,
                    $user->fullPhone)->afterCommit();
            }
        }

        if (count($tokensUsers)) {

            $data = [
                'type' => 'medicineReminder',
                'title' => $title,
                'body' => $body,
                'mediaType' => 'image',
                'media' => $fileUploaded ? \Storage::disk('s3')->url($fileUploaded) : '',
                'url' => '/notifications',
                'reminder_id' => $reminder->id,


            ];

            SendAppNotificationJob::dispatch($title, $body, $tokensUsers, $data)->afterCommit();


        }

        //$reminder->delete();
        $reminder->update(['status_notification' => MedicineReminderStatusNotification::SENT]);

        if (request()->wantsJson()) {


            return response([], 204);


        }

        flash('Recordatorio enviado', 'success');

        return back();
    }

    public function contacted(MedicineReminder $reminder)
    {
        $reminder->update(['status' => MedicineReminderStatus::CONTACTED]);

        flash('Recordatorio marcado como contactado', 'success');

        return back();
    }
}
