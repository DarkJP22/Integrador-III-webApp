<?php

namespace App\Http\Controllers\Api;


use App\Http\Resources\PatientUserResource;
use App\PatientUser;
use App\Repositories\PatientRepository;
use App\Patient;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Support\Facades\DB;


class UserMedicAuthorizationsController extends Controller
{

    public function __construct(protected PatientRepository $patientRepo, protected UserRepository $userRepo)
    {
        $this->middleware('auth');
    }

    public function index(User $user)
    {
        $medicAuthorizations = PatientUser::query()
            ->with('user', 'patient')
            ->whereIn('patient_id', $user->patients->pluck('id'))
            ->where('authorization', 1)
            ->whereNotNull('authorized_at')
            ->paginate(10);


        return $medicAuthorizations;
    }


    public function destroy(User $user, string|int $authorizationId)
    {

        PatientUser::findOrFail($authorizationId)->update([
            'authorization' => 0,
        ]);

        return response()->json(['message' => 'Autorización eliminada correctamente!']);
    }


}
