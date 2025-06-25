<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Medicine;
use App\Patient;
use App\Http\Controllers\Controller;

class MedicineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Patient $patient)
    {

        $medicines = $patient->medicines()->with('dosesreminder')->orderBy('created_at', 'DESC')->get();


        return $medicines;
    }

    public function store(Patient $patient)
    {
        request()->validate([
            'name' => 'required'

        ]);


        $medicine = $patient->medicines()->create([
            'name' => request('name'),
            'user_id' => auth()->id(),
            'creator_type' => 'App\Patient',
            'creator_id' => $patient->id,
        ]);

        return $medicine;
    }

    public function destroy(Medicine $medicine)
    {
        $result = $medicine->delete();

        if ($result === true) {

            return response([], 204);
        }

        return response(['message' => 'Error al eliminar'], 422);
    }
}
