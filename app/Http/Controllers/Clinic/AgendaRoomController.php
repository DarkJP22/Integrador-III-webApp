<?php

namespace App\Http\Controllers\Clinic;


use Carbon\Carbon;
use App\Repositories\AppointmentRepository;
use App\Repositories\PatientRepository;
use App\Http\Controllers\Controller;
use App\Optreatment;
use App\Repositories\MedicRepository;
use App\Room;

class AgendaRoomController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AppointmentRepository $appointmentRepo, PatientRepository $patientRepo, MedicRepository $medicRepo)
    {
        $this->middleware('auth');
        $this->appointmentRepo = $appointmentRepo;
        $this->patientRepo = $patientRepo;
        $this->medicRepo = $medicRepo;
    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $office = auth()->user()->offices->first();
        $search['q'] = $q = request('q');


        $rooms = Room::where('office_id', $office->id)->search($search)->paginate();



        if (request('room'))
            $room = Room::find(request('room'));
        else
            $room = null;

        $optreatments = Optreatment::all();

        return view('clinic.agenda.rooms', compact('rooms', 'room', 'office', 'q', 'optreatments'));
    }
}
