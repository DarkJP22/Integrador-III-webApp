<?php

namespace App\Http\Controllers\Pharmacy;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\Repositories\PatientRepository;
use App\Patient;
use App\Http\Requests\PatientRequest;
use App\MediaTag;
use App\Services\FarmaciaService;
use App\User;


class MarketingController extends Controller
{
    public function __construct(PatientRepository $patientRepo)
    {

        $this->patientRepo = $patientRepo;


        $this->middleware('auth');


    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        if (!auth()->user()->hasRole('farmacia')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['province'] = request('province');
        $search['conditions'] = request('conditions');


        $pharmacy = auth()->user()->pharmacies->first();

        $patients = $pharmacy->patients()->Search($search)->with('pharmacies')->paginate();

        $padecimientos = MediaTag::orderBy('name', 'ASC')->get();


        return view('pharmacy.marketing.index', compact('patients', 'pharmacy', 'search', 'padecimientos'));

    }


}
