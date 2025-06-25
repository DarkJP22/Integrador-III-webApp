<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Patient;
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

    /**
     * Guardar consulta(cita)
     */
    public function store()
    {
        $schedule = $this->scheduleRepo->store(request()->all());

        if (!$schedule) return '';

        try {
            $settings = $schedule->user->getAllSettings();
            $maxTime = $settings['maxTime'];
            $endSchedule = $schedule->end;

            $timeMaxTime = explode(':', $maxTime)[0];
            $endSchedule = explode('T', $endSchedule)[1];
            $timeEndSchedule = explode(':', $endSchedule)[0];

            if ($timeEndSchedule > $timeMaxTime) {
                $schedule->user->setSettings([
                    'maxTime' => $endSchedule
                ]);

            }
        } catch (\Throwable $th) {
            //throw $th;
        }

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
     * Eliminar consulta(cita)
     */
    public function destroy($id)
    {

        $schedule = $this->scheduleRepo->delete($id);

        if (request()->wantsJson()) {

            if ($schedule === true) {

                return response('Horario eliminado correctamente !', 204);
            }

            return response(['message' => 'No se puede eliminar el horario ya que tiene citas agendas'], 422);

        }



        return back();

    }

    public function copy()
    {
        $fechaInicioSemanaActual = Carbon::now()->startOfWeek();
        $fechaFinSemanaActual = Carbon::now()->endOfWeek();

        $fechaInicioSemanaCopiar = Carbon::parse(request('dateweek'))->startOfWeek();
        $fechaFinSemanaCopiar = Carbon::parse(request('dateweek'))->endOfWeek();

        $search['date1'] = $fechaInicioSemanaActual;
        $search['date2'] = $fechaFinSemanaActual;

        $schedules = $this->scheduleRepo->findAllByDoctorWithoutPagination(auth()->id(), $search);

        if ($schedules->count()) {

            flash('No se puede copiar por que ya hay un horario programado en esta semana, verifica!', 'danger');
            return back();
        }

        $search['date1'] = $fechaInicioSemanaCopiar;
        $search['date2'] = $fechaFinSemanaCopiar;

        $copiedSchedules = 0;
        $copySchedules = $this->scheduleRepo->findAllByDoctorWithoutPagination(auth()->id(), $search);

        foreach ($copySchedules as $scheduleToCopy) {

            $fechaHorario = Carbon::parse($scheduleToCopy->date);
            $fechaHorarioInicio = Carbon::parse($scheduleToCopy->start);
            $fechaHorarioFin = Carbon::parse($scheduleToCopy->end);

            while (!$fechaHorario->between($fechaInicioSemanaActual, $fechaFinSemanaActual)) {

                $fechaHorario->addWeek(); // le sumamos una semana hasta que la fecha este entre el rango de la semana actual

                $fechaHorarioInicio->addWeek();
                $fechaHorarioFin->addWeek();

                if ($fechaHorario->month > $fechaFinSemanaActual->month || $fechaHorario->year > $fechaFinSemanaActual->year)
                    break;

            }

            if ($scheduleToCopy->date != $fechaHorario->toDateTimeString() && ($fechaHorario->between($fechaInicioSemanaActual, $fechaFinSemanaActual))) {
                Schedule::create([
                    'user_id' => $scheduleToCopy->user_id,
                    'office_id' => $scheduleToCopy->office_id,
                    'date' => $fechaHorario->toDateTimeString(),
                    'start' => $fechaHorarioInicio->toDateString() . 'T' . $fechaHorarioInicio->toTimeString(),
                    'end' => $fechaHorarioFin->toDateString() . 'T' . $fechaHorarioFin->toTimeString(),
                    'allDay' => $scheduleToCopy->allDay,
                    'title' => $scheduleToCopy->title,
                    'backgroundColor' => $scheduleToCopy->backgroundColor,
                    'borderColor' => $scheduleToCopy->borderColor,
                    'status' => $scheduleToCopy->status
                ]);

                $copiedSchedules++;
            }


        }

        flash($copiedSchedules . ' Horario(s) copiado(s) con exito!', 'success');

        return back();
    }

    public function create()
    {
        $this->authorize('viewAny', Schedule::class);

        if (!auth()->user()->hasRole('medico')) {
            return redirect('/');
        }

        $wizard = null;
        $clinic_id = request('clinic');

        if (request('wizard')) {
            $wizard = 1;
        }

        $weeks = getWeeksOfMonth(Carbon::now()->month);
        $slotDurations = collect(config('gpsmedica.slot_durations', []))->transform(fn ($slot) => (object) $slot);

        return view('schedules.create', compact('wizard', 'clinic_id', 'weeks', 'slotDurations'));
    }
}
