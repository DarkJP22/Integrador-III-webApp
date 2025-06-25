<?php

namespace App;

use App\Interfaces\GenerateDataDownload;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class GenerateMedicData implements GenerateDataDownload
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
        $pdf = Pdf::loadView('pdf.medic_download_data', [
            'user' => $this->user->load(['appointments.diseaseNotes', 'appointments.vitalSigns']),
        ]);

        $filePath = "{$this->folder}/{$this->user->id}/historial_consultas.pdf";
        Storage::disk('local')->put($filePath, $pdf->output());

        $sources = [
            storage_path('app/'.$filePath) => 'historial_consultas.pdf',

        ];

        $offices = $this->user->offices;
        foreach ($offices as $office) {
            foreach ($office->configFactura as $configFactura) {
                $path = storage_path("app/facturaelectronica/{$configFactura->id}");
                $sources[$path] = "facturaelectronica-".Str::slug($office->id.'-'.$office->name);
            }
        }

        createZip($sources, storage_path("app/{$this->folder}/data_{$this->user->id}.zip"));

    }
}
