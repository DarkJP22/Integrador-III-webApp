<?php

namespace App\Imports;

use App\Drug;
use App\Enums\DrugStatus;
use App\Notifications\ImportHasFailedNotification;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Row;

class DrugsImport implements OnEachRow, WithChunkReading, WithHeadingRow, ShouldQueue, WithEvents
{
    use Importable;

    public function __construct(protected User $importedBy)
    {
    }

    public function onRow(Row $row): void
    {
        $data = $row->toArray(null, true); // calculate formulas

        $data = array_filter($data, 'filled');

        if (isset($data['nombre'])) {

            if (Drug::where('name', $data['nombre'])->exists()) {
                return;
            }

            $path = $this->uploadImage($data);

            $drug = Drug::updateOrCreate(
                [
                    'name' => $data['nombre'],
                ],
                [
                    'description' => $data['descripcion'] ?? null,
                    'laboratory' => $data['laboratorio'] ?? null,
                    'presentation' => $data['tipo_presentacion'] ?? null,
                    'active_principle' => $data['principios_activos'] ?? null,
                    'image_path' => $path,
                    'status' => DrugStatus::PUBLISHED,
                    'creator_id' => $this->importedBy->id,
                ]);
        }
    }

    public function chunkSize(): int
    {
        return 250;
    }

    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function (ImportFailed $event) {
                $this->importedBy->notify(new ImportHasFailedNotification(__('La importación de medicamentos ha fallado!')));
            },
        ];
    }

    protected function uploadImage(array $data): ?string
    {
        try {
            $path = null;
            $response = Http::timeout(5)->get($data['imagen']);
            if ($response->ok() && $response->body()) {
                $imageContent = $response->body();
                $fileName = Str::slug($data['nombre'].'-'.rand()).'.jpg';
                if (Storage::disk('s3')->put('drugs/'.$fileName, $imageContent)) {
                    $path = Storage::path('drugs/'.$fileName);
                }
            }
            return $path;
        } catch (ConnectionException $e) {
            return null;
        }

    }
}
