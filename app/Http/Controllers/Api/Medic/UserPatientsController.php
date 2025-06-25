<?php

namespace App\Http\Controllers\Api\Medic;

use App\Actions\RegisterPatient;
use App\Actions\ShareLinkAppMobileAction;
use App\Actions\UpdatePatient;
use App\Repositories\PatientRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Patient;
use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Labresult;
use Illuminate\Support\Facades\Validator;


class UserPatientsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected PatientRepository $patientRepo)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $search['q'] = request('q');

        if (request('q')) {
            $patients = Patient::search(request(['q']));
        } else {
            $patients = auth()->user()->patients();
        }

        if (request('labresults')) {
            $patients = $patients->search(request(['q']))->withCount([
                'labresults as unread_labresults_count' => function ($query) {
                    $query->whereNull('read_at')
                        ->where('medic_id', auth()->user()->id);
                },
            ]);

            return PatientResource::collection($patients->orderBy(
                Labresult::select('created_at')
                    ->where('medic_id', auth()->user()->id)
                    ->whereColumn('labresults.patient_id', 'patients.id')->latest()->limit(1),
                'Desc'
            )
                ->paginate(10));
        }

        if (request()->wantsJson()) {
            return PatientResource::collection($patients->latest()->paginate(10));
        }
    }

    /**
     * Guardar paciente
     */
    public function store(Request $request, RegisterPatient $registerPatient)
    {

        $patient = $registerPatient($request->all(), [
            'tipo_identificacion' => ['required'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required'],
            'province' => ['required'],
            'canton' => ['required'],
            'district' => ['required'],
        ]);

        return $patient->refresh();
    }

    /**
     * Actualizar Paciente
     */
    public function update(Request $request, Patient $patient, UpdatePatient $updatePatient)
    {

        return $updatePatient($patient, $request->all());
    }

    public function destroy(Patient $patient)
    {
        $patient = $this->patientRepo->delete($patient->id);

        if ($patient === true) {

            return response([], 204);
        }

        return response(['message' => 'No se puede eliminar paciente por que tiene citas asignadas'], 403);
    }
}
