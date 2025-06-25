<?php

namespace App;

use App\Interfaces\GenerateDataDownload;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class GeneratePatientData implements GenerateDataDownload
{
    /**
     * Create a new class instance.
     */
    public function __construct(public User $user, public string $folder = 'downloads-users')
    {
        //
    }

    public function generate(): void
    {
        $pdf = Pdf::loadView('pdf.user_download_data', [
            'user' => $this->user->load(['patients.appointments.diseaseNotes', 'patients.appointments.vitalSigns']),
        ]);

        $filePath = "{$this->folder}/{$this->user->id}/historial_expediente.pdf";
        Storage::disk('local')->put($filePath, $pdf->output());

//        $sources = [
//            storage_path('app/'.$filePath) => 'historial_consultas.pdf',
//
//        ];

        foreach ($this->user->patients as $patient) {

            foreach ($patient->labresults as $labresult) {
                $pathLabresultS3 = 'patients/' . $patient->id . '/labresults/' . $labresult->id . '/' . $labresult->name;
                Storage::disk('local')->put("{$this->folder}/{$this->user->id}/resultados-".Str::slug($patient->id.'-'.$patient->ide)."/{$labresult->name}", Storage::disk('s3')->get($pathLabresultS3));


            }

        }

        $sources = [
            storage_path("app/{$this->folder}/{$this->user->id}") => 'user_data',
        ];


        createZip($sources, storage_path("app/{$this->folder}/data_{$this->user->id}.zip"));
    }
}
