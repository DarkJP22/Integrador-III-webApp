<?php

namespace App\Http\Controllers;

use App\Events\LabResultCreatedEvent;
use App\Jobs\SendAppNotificationJob;
use Illuminate\Http\Request;
use App\Labresult;
use App\Patient;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LabResultController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Agregar medicamentos a pacientes
     */
    public function store(Patient $patient)
    {
        $data = $this->validate(request(), [
            'date' => 'required',
            'medic_id' => 'required',
            'file' => 'required|max:5000|mimes:jpg,jpeg,bmp,png,pdf,xls,xlsx,doc,docx',
            'description' => 'nullable',
        ]);

        $data['patient_id'] = $patient->id;

        $file = request()->file('file');
        $name = $file->getClientOriginalName();
        $ext = $file->guessClientExtension();
        $onlyName = Str::slug(pathinfo($name)['filename'], '-');
        $data['name'] = $onlyName;

        $labresult = Labresult::create($data);

        $fileUploaded = $file->storeAs('patients/' . $patient->id . '/labresults/' . $labresult->id, $onlyName . '.' . $ext, 's3');

        $labresult->name = $onlyName . '.' . $ext;
        $labresult->save();

        $patientUserPushtokens = array_filter(
            $patient->user()->whereHas('roles', function ($query) {
                $query->where('name', 'paciente');
            })->whereNotNull('push_token')
                ->pluck('push_token')->all()
        );

        $labresult->user_id = $patient->user()->whereHas('roles', function ($query) {
            $query->where('name', 'paciente');
        })->where('ide', $patient->ide)->first()?->id;

        $labresult->save();

        $medic =  User::find($labresult->medic_id);

        // broadcast pusher
        if($medic){
            try {
                LabResultCreatedEvent::dispatch(auth()->user(), $labresult);
            } catch (\Exception $ex) {
                Log::error('ERROR BROADCAST: '.json_encode($ex->getMessage()));
            }
        }

        if ($medic && $medic->push_token) {
            $title = 'Resultado de Laboratorio Agregado';
            $message = 'Se agrego el resultado de laboratorio del paciente ' . $patient->fullname . '.';
            $extraData = [
                'type' => 'labresult',
                'title' => $title,
                'body' => $message,
                'url' => '/medic/lab-results',
                'resource_id' => $labresult->id
            ];

            SendAppNotificationJob::dispatch($title, $message, [$medic->push_token], $extraData);
        }

        if (count($patientUserPushtokens)) {
            $title = 'Resultado de Laboratorio Agregado';
            $message = 'Tienes un nuevo resultado de laboratorio en tu expediente.';
            $extraData = [
                'type' => 'labresult',
                'title' => $title,
                'body' => $message,
                'url' => '/patient/expedient',
                'resource_id' => $labresult->id
            ];

            SendAppNotificationJob::dispatch($title, $message, $patientUserPushtokens, $extraData);
        }

        return $labresult;
    }

    /**
     * Eliminar medicamentos a pacientes
     */
    public function destroy($id)
    {
        $result = Labresult::find($id);
        $fileToDelete = "/patients/" . $result->patient_id . "/labresults/" . $result->id . "/" . $result->name;

        $result->delete();


        Storage::disk('s3')->delete($fileToDelete);

        return '';
    }
}
