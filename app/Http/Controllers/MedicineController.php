<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Medicine;
use App\Patient;

class MedicineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(Patient $patient)
    {

        return $patient->medicines()->with('dosesreminder')->orderBy('created_at', 'DESC')->paginate(10);

    }

    public function store(Patient $patient)
    {
        request()->validate([
            'name' => 'required'

        ]);


        $medicine = $patient->medicines()->create([
            'name' => request('name'),
            'receta' => request('receta'),
            'user_id' => auth()->id(),
        ]);

        return $medicine;
    }

    public function receta(Medicine $medicine)
    {

        $medicine->update([
            'receta' => request('receta'),

        ]);

        return $medicine;
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
