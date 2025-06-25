<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Office;
use App\Room;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class OfficeRoomsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(Office $office = null)
    {

        $search['q'] = request('q');

        if (!$office) {
            $office = auth()->user()->offices->first();

        }

        if (request()->wantsJson()) {
            return $office->rooms;
        }

        return view('clinic.rooms.index', [
            'rooms' => $office->rooms()->search($search)->paginate(10),
            'search' => $search
        ]);

    }

    public function edit(Room $room)
    {
        if (!auth()->user()->hasRole('clinica') || $room->office_id != auth()->user()->Offices->first()->id) {
            return redirect('/');
        }

        return view('clinic.rooms.edit', compact('room'));
    }

    public function store(Office $office = null)
    {
        if (!$office) {
            $office = auth()->user()->offices->first();
        }

        $data = $this->validate(request(), [
            'name' => [
                'required', Rule::unique('rooms', 'name')->where(function ($query) use ($office) {
                    return $query->where('office_id', $office->id);
                })
            ]
        ]);

        $office->rooms()->create($data);

        return redirect('/clinic/rooms');


    }

    public function create()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        return view('clinic.rooms.create');
    }

    public function update(Office $office = null, Room $room)
    {
        if (!$office) {
            $office = auth()->user()->offices->first();
        }

        $data = $this->validate(request(), [
            'name' => [
                'required', Rule::unique('rooms', 'name')->where(function ($query) use ($office) {
                    return $query->where('office_id', $office->id);
                })->ignore($room->id)
            ]
        ]);


        $room->update($data);

        return redirect('/clinic/rooms');


    }

    public function destroy(Office $office = null, Room $room)
    {
        if (!$office) {
            $office = auth()->user()->offices->first();
        }

        if (!auth()->user()->hasRole('clinica') || $room->office_id != $office->id) {
            return redirect('/');
        }

        $room->delete();

        return redirect()->back();


    }

    public function checkAvailability(Office $office, Room $room)
    {

        $date1 = Carbon::parse(request('date1'));
        $user_settings = auth()->user()->getSettings(['slotDuration']);
        $slotDuration = $user_settings['slotDuration'] ?? '00:30:00';
        $stepInterval = CarbonInterval::createFromFormat('H:i:s', $slotDuration);
        $start = $date1->toDateTimeString();
        $end = $date1->addMinutes($stepInterval->totalMinutes)->toDateTimeString();

        return $appoinments = Appointment::where('office_id', $office->id)
            ->where('room_id', $room->id)
            ->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") <= ?', [$start])
            ->whereRaw('DATE_FORMAT(end, "%Y-%m-%d %H:%i:%s") >= ?', [$end])
            ->count();


    }
}
