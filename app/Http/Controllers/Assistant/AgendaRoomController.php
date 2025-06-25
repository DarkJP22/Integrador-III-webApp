<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use App\Optreatment;
use App\Room;

class AgendaRoomController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        if (!auth()->user()->hasRole('asistente')) {
            return redirect('/');
        }

        $office = auth()->user()->clinicsAssistants->first();
        $search['q'] = $q = request('q');


        $rooms = Room::where('office_id', $office->id)->search($search)->paginate();



        if (request('room'))
            $room = Room::find(request('room'));
        else
            $room = null;

        $optreatments = Optreatment::all();

        return view('assistant.agenda.rooms', compact('rooms', 'room', 'office', 'q', 'optreatments'));
    }
}
