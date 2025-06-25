<?php

namespace App\Http\Controllers\Api\Medic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ScheduleRepository;
use App\Schedule;

class ScheduleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ScheduleRepository $scheduleRepo)
    {
        $this->middleware('auth');
        $this->scheduleRepo = $scheduleRepo;
    }

    public function show(Schedule $schedule)
    {
        return $schedule->load('office', 'user');
    }

    /**
     * Guardar consulta(cita)
     */
    public function storeAll()
    {
        $data = $this->validate(request(), [
            'schedules.*.title' => ['required'],
            'schedules.*.date' => ['required', 'date'],
            'schedules.*.start' => ['required', 'date'],
            'schedules.*.end' => ['required', 'date'],
            'schedules.*.office_id' => ['required'],
            'schedules.*.user_id' => ['required'],
            'schedules.*.allDay' => ['sometimes'],
            'schedules.*.backgroundColor' => ['sometimes'],
            'schedules.*.borderColor' => ['sometimes'],
            'slotDuration' => ['sometimes'],
        ]);

        foreach ($data['schedules'] as $schedule) {
            $schedule['backgroundColor'] = $schedule['backgroundColor'] ?? '#67BC9A';
            $schedule['borderColor'] = $schedule['borderColor'] ?? '#67BC9A';
            $sc[] = $this->scheduleRepo->store($schedule);
        }

        if(isset($data['slotDuration'])){
            auth()->user()->setSettings([
                'slotDuration' => $data['slotDuration']
            ]);

        }

        return $sc;
    }

    /**
     * Guardar consulta(cita)
     */
    public function store()
    {
        $data = $this->validate(request(), [
            'title' => ['required'],
            'date' => ['required', 'date'],
            'start' => ['required'],
            'end' => ['required'],
            'office_id' => ['required'],
            'user_id' => ['required'],
            'allDay' => ['sometimes'],
            'backgroundColor' => ['sometimes'],
            'borderColor' => ['sometimes'],
        ]);

        $data['backgroundColor'] = request('backgroundColor') ?? '#67BC9A';
        $data['borderColor'] = request('borderColor') ?? '#67BC9A';

        $schedule = $this->scheduleRepo->store($data);

        if (!$schedule) return '';

        $schedule['office'] = $schedule->office;
        $schedule['user'] = $schedule->user;

        return $schedule;
    }

    /**
     * Actualizar consulta(cita)
     */
    public function update($id)
    {

        $schedule = $this->scheduleRepo->update($id, request()->all());

        if ($schedule) {
            $schedule['office'] = $schedule->office;
            $schedule['user'] = $schedule->user;
        }

        return $schedule;
    }


    /**
     * Eliminar consulta(cita) ajax desde calendar
     */
    public function destroy($id)
    {

        $schedule = $this->scheduleRepo->delete($id);

        //if($schedule !== true)  return $schedule; //no se elimino correctamente
        if ($schedule === true) {
            return response(['message' => 'Horario Eliminado Correctamente'], 204);
        }


        return response(['message' => 'Horario Eliminado Correctamente'], 403);
    }
}
