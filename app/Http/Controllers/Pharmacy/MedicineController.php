<?php

namespace App\Http\Controllers\Pharmacy;

use Illuminate\Http\Request;
use App\Patient;
use App\Http\Controllers\Controller;
use App\Medicine;
use App\Pmedicine;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(Patient $patient)
    {
        $pharmacy = auth()->user()->pharmacies->first();

        return $patient->medicines()
                ->with('dosesreminder')
                ->where('creator_id', $pharmacy->id)
                ->where('creator_type', get_class($pharmacy))
                ->orderBy('created_at', 'DESC')
                ->paginate(10);

    }

    public function store(Patient $patient)
    {
        request()->validate([
            'name' => 'required',
            'receta' => 'nullable',
            'date_purchase' => 'nullable'

        ]);
        // dd(request('date_purchase'));
        $pharmacy = auth()->user()->pharmacies->first();

        // ya no se crea el medicamento al paciente por que solo es para recordatorios de pharmacias 13-07-2023
//        $medicine = $patient->medicines()->create([
//            'name' => request('name'),
//            'receta' => request('receta'),
//            'date_purchase' => request('date_purchase'),
//            'user_id' => auth()->user()->id,
//            //'pharmacy_id' => $pharmacy->id,
//        ]);

        $medicine = $patient->medicines()->create([
            'name' => request('name'),
            'receta' => request('receta'),
            'date_purchase' => request('date_purchase'),
            'user_id' => auth()->user()->id,
            'creator_id' => $pharmacy->id,
            'creator_type' => get_class($pharmacy),
            'remember_days' => 28,
            'remember' => 0,
            'requested_units' => 1,
            'active_remember_for_days' => 30,
        ]);

        return $medicine->load('dosesreminder');
    }

    public function transfer(Patient $patient)
    {
        request()->validate([
            'items' => 'required',


        ]);
        // dd(request('date_purchase'));
        $pharmacy = auth()->user()->pharmacies->first();


        foreach (request('items') as $item) {

            $patient->medicines()->create([
                'name' => $item['name'],
                'receta' => $item['receta'],
                'date_purchase' => isset($item['purchase_date']) ? $item['purchase_date'] : Carbon::now()->toDateString(),
                'user_id' => auth()->user()->id,
                'creator_id' => $pharmacy->id,
                'creator_type' => get_class($pharmacy),
            ]);
        }

        $medicines = $patient->medicines()->where('creator_id', $pharmacy->id)->where('creator_type', get_class($pharmacy))->orderBy('created_at', 'DESC')->get();

        if (request()->wantsJson()) {


            return response($medicines, 200);

        }
    }

    public function receta(Medicine $medicine)
    {

        $medicine->update([
            'receta' => request('receta'),

        ]);

        return $medicine;
    }

    public function update(Medicine $medicine)
    {
        $reminder_start = Carbon::parse(request('date_purchase'));
        $reminder_end = (request('active_remember_for_days') && request('active_remember_for_days') !== -1) ? $reminder_start->copy()->addDays(request('active_remember_for_days')) : null;

        request()->validate([
            'requested_units' => ['numeric', Rule::when(request('remember') && ($reminder_end || request('active_remember_for_days') === -1), ['min:1'])],
            'active_remember_for_days' => 'numeric',
            'remember_days' => 'numeric|min:0',
        ]);


        $medicine->update([
            'remember' => request('remember'),
            'remember_days' => request('remember_days'),
            'date_purchase' => request('date_purchase'),
            'active_remember_for_days' => request('active_remember_for_days'),
            'requested_units' => request('requested_units'),
            'reminder_start' => $reminder_start,
            'reminder_end' => $reminder_end,

        ]);

        return $medicine->load('dosesreminder');
    }

    public function destroy(Medicine $medicine)
    {
        $result = $medicine->delete();


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'Error al eliminar'], 422);

        }

        return Redirect('/medicines');
    }
}
