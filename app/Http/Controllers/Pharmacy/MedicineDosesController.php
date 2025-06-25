<?php

namespace App\Http\Controllers\Pharmacy;

use App\Dosereminder;
use App\Http\Controllers\Controller;
use App\Medicine;
use Carbon\Carbon;

class MedicineDosesController extends Controller
{
    public function store(Medicine $medicine)
    {
        $validated = request()->validate([
            'medicine_id' => ['required'],
            'schema' => ['required'],
            'hours' => ['required'],
            'start_at' => ['required', 'date'],
            'days' => ['required'],
        ]);

        $validated['end_at'] = Carbon::parse($validated['start_at'])->addDays($validated['days'])->toDateTimeString();
        $patient = $medicine->patient;

        return $patient->dosereminders()->create($validated);
    }

    public function update(Medicine $medicine, Dosereminder $dose)
    {
        $validated = request()->validate([
            'schema' => ['required'],
            'hours' => ['required'],
            'start_at' => ['required', 'date'],
            'days' => ['required'],
        ]);

        $validated['end_at'] = Carbon::parse($validated['start_at'])->addDays($validated['days'])->toDateTimeString();
        $dose->update($validated);

        return $dose;
    }

}
