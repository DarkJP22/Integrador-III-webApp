<?php

namespace App\Http\Controllers\Assistant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Patient;
use App\Repositories\ScheduleRepository;
use App\Schedule;
use App\Http\Controllers\Controller;
use App\User;
use App\Repositories\MedicRepository;
class ScheduleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ScheduleRepository $scheduleRepo, MedicRepository $medicRepo)
    {
        $this->middleware('auth');
        $this->scheduleRepo = $scheduleRepo;
        $this->medicRepo = $medicRepo;

    }


    public function create(User $medic)
    {
        if (!auth()->user()->hasRole('asistente')) {
            return redirect('/');
        }
        $office = auth()->user()->clinicsAssistants->first();
        
        $wizard = null;
    
        if (request('wizard')) {
            $wizard = 1;
        }
         $weeks = getWeeksOfMonth(Carbon::now()->month);
         $slotDurations = collect(config('gpsmedica.slot_durations', []))->transform(fn ($slot) => (object) $slot);
        

        return view('assistant.schedules.create', compact('wizard', 'medic','office', 'weeks', 'slotDurations'));
    }

    public function monthly()
    {
        if (!auth()->user()->hasRole('asistente')) {
            return redirect('/');
        }

        $office = auth()->user()->clinicsAssistants->first();
        $medics = $this->medicRepo->findAllByOfficeWithoutPaginate($office->id);
        $weeks = getWeeksOfMonth(Carbon::now()->month);

        return view('assistant.schedules.monthly', compact('office','medics','weeks'));
    }

    public function monthlySchedules()
    {

        $search = request()->all();
        
        $schedules = Schedule::where('office_id', $search['office']);

        if (isset($search['from']) && $search['from'] != '') {
            // dd($search['date2']);

            $date1 = $search['from'];
            $date2 = (isset($search['to']) && $search['to'] != '') ? $search['to'] : $search['from'];
            $date2 = $date2;

            $schedules = $schedules->where([
                ['schedules.date', '>=', $date1],
                ['schedules.date', '<=', $date2->endOfDay()]
            ]);
        }

       
        return $schedules->with('user')->get();
    }

    
    public function getSchedulesFromClinic($search){
        $schedules = Schedule::where('office_id', $search['office']);

        if (isset($search['date1']) && $search['date1'] != '') {
            // dd($search['date2']);

            $date1 = $search['date1'];
            $date2 = (isset($search['date2']) && $search['date2'] != '') ? $search['date2'] : $search['date1'];
            $date2 = $date2;

            $schedules = $schedules->where([
                ['schedules.date', '>=', $date1],
                ['schedules.date', '<=', $date2->endOfDay()]
            ]);
        }

       
        return $schedules->with('user')->get();
    }
    public function monthlyCopy()
    {
        $fechaInicioSemanaActual = Carbon::now()->startOfWeek();
        $fechaFinSemanaActual = Carbon::now()->endOfWeek();
     
        $fechaInicioSemanaCopiar = Carbon::parse(request('dateweek'))->startOfWeek();
        $fechaFinSemanaCopiar = Carbon::parse(request('dateweek'))->endOfWeek();

        $search['date1'] = $fechaInicioSemanaActual;
        $search['date2'] = $fechaFinSemanaActual;
        
        $office = auth()->user()->clinicsAssistants->first();
        $search['office'] = $office->id;
        
        $schedules = $this->getSchedulesFromClinic($search);

        if ($schedules->count()) {

            flash('No se puede copiar por que ya hay un horario programado en esta semana, verifica!', 'danger');
            return back();
        }

        $search['date1'] = $fechaInicioSemanaCopiar;
        $search['date2'] = $fechaFinSemanaCopiar;

        $copiedSchedules = 0;
        $copySchedules = $this->getSchedulesFromClinic($search);

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

    // public function copy(User $medic)
    // {
    //     $fechaInicioSemanaActual = Carbon::now()->startOfWeek();
    //     $fechaFinSemanaActual = Carbon::now()->endOfWeek();
     
    //     $fechaInicioSemanaCopiar = Carbon::parse(request('dateweek'))->startOfWeek();
    //     $fechaFinSemanaCopiar = Carbon::parse(request('dateweek'))->endOfWeek();

    //     $search['date1'] = $fechaInicioSemanaActual;
    //     $search['date2'] = $fechaFinSemanaActual;
        
    //     $schedules = $this->scheduleRepo->findAllByDoctorWithoutPagination($medic->id, $search);

    //     if ($schedules->count()) {

    //         flash('No se puede copiar por que ya hay un horario programado en esta semana, verifica!', 'danger');
    //         return back();
    //     }

    //     $search['date1'] = $fechaInicioSemanaCopiar;
    //     $search['date2'] = $fechaFinSemanaCopiar;

    //     $copiedSchedules = 0;
    //     $copySchedules = $this->scheduleRepo->findAllByDoctorWithoutPagination($medic->id, $search);

    //     foreach ($copySchedules as $scheduleToCopy) {
           
    //         $fechaHorario = Carbon::parse($scheduleToCopy->date);
    //         $fechaHorarioInicio = Carbon::parse($scheduleToCopy->start);
    //         $fechaHorarioFin = Carbon::parse($scheduleToCopy->end);
        
    //         while (!$fechaHorario->between($fechaInicioSemanaActual, $fechaFinSemanaActual)) {
         
    //             $fechaHorario->addWeek(); // le sumamos una semana hasta que la fecha este entre el rango de la semana actual
           
    //             $fechaHorarioInicio->addWeek();
    //             $fechaHorarioFin->addWeek();

    //             if ($fechaHorario->month > $fechaFinSemanaActual->month || $fechaHorario->year > $fechaFinSemanaActual->year)
    //                 break;

    //         }
          
    //         if ($scheduleToCopy->date != $fechaHorario->toDateTimeString() && ($fechaHorario->between($fechaInicioSemanaActual, $fechaFinSemanaActual))) {
    //             Schedule::create([
    //                 'user_id' => $scheduleToCopy->user_id,
    //                 'office_id' => $scheduleToCopy->office_id,
    //                 'date' => $fechaHorario->toDateTimeString(),
    //                 'start' => $fechaHorarioInicio->toDateString() . 'T' . $fechaHorarioInicio->toTimeString(),
    //                 'end' => $fechaHorarioFin->toDateString() . 'T' . $fechaHorarioFin->toTimeString(),
    //                 'allDay' => $scheduleToCopy->allDay,
    //                 'title' => $scheduleToCopy->title,
    //                 'backgroundColor' => $scheduleToCopy->backgroundColor,
    //                 'borderColor' => $scheduleToCopy->borderColor,
    //                 'status' => $scheduleToCopy->status
    //             ]);

    //             $copiedSchedules++;
    //         }
            

    //     }

    //     flash($copiedSchedules . ' Horario(s) copiado(s) con exito!', 'success');

    //     return back();
    // }
   
   
}
  
