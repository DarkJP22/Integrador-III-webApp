<?php

namespace App\Http\Controllers\Beautician;

use App\Appointment;
use App\Http\Controllers\Controller;
use App\Schedule;
use App\User;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;

class AvailableHoursController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(User $user)
    {

        $data = $this->validate(request(), [
            'date' => ['required', 'date'],
            'room_id' => ['required'],
            'office_id' => ['required'],

        ]);

        $date1 = Carbon::parse(request('date'))->startOfDay();
        $date2 = Carbon::parse(request('date'))->endOfDay();
        $start = $date1->toDateTimeString();
        $end = $date2->toDateTimeString();
 
        $schedules = Schedule::where('office_id', $data['office_id'])
            ->where('user_id', $user->id)
            ->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") >= ?', [$start])
            ->whereRaw('DATE_FORMAT(end, "%Y-%m-%d %H:%i:%s") <= ?', [$end])->get();

        if (!$schedules->count()) {
            return [];
        }

        $appoinments = Appointment::where('office_id', $data['office_id'])
            ->where('room_id', $data['room_id'])
            ->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") >= ?', [$start])
            ->whereRaw('DATE_FORMAT(end, "%Y-%m-%d %H:%i:%s") <= ?', [$end])
            ->get();

        foreach ($schedules as $schedule) {
            $startHour = Carbon::parse($schedule->start)->secondsSinceMidnight();
            $endHour = Carbon::parse($schedule->end)->secondsSinceMidnight();
            $user_settings = $user->getSettings(['slotDuration']);
            $slotDuration = $user_settings['slotDuration'] ?? '00:30:00';
            $stepInterval = CarbonInterval::createFromFormat('H:i:s', $slotDuration);

            $hours = collect($this->get_hours_range($startHour, $endHour, $stepInterval->totalSeconds));

            foreach ($hours as $key => $value) {
                $start = Carbon::parse(request('date'))->setTimeFromTimeString($key)->toDateTimeLocalString();
                $end =  Carbon::parse(request('date'))->setTimeFromTimeString($key)->addSeconds($stepInterval->totalSeconds)->toDateTimeLocalString();

                $item = [
                    'label' => $value,
                    'value' => $key,
                    'disabled' => $this->isReserved($start, $end, $appoinments)
                ];
                $availableHours[] = $item;
            }
        }

        return $availableHours;
    }

    function get_hours_range($start = 0, $end = 86400, $step = 1800, $format = 'g:i a')
    {
        $times = array();
        foreach (range($start, $end, $step) as $timestamp) {
            $hour_mins = gmdate('H:i', $timestamp);
            if (!empty($format))
                $times[$hour_mins] = gmdate($format, $timestamp);
            else $times[$hour_mins] = $hour_mins;
        }
        return $times;
    }

    function isReserved($startSchedule, $endSchedule, $appointments)
    {

        foreach ($appointments as $appointment) {
            if ($appointment->end > $startSchedule && $appointment->start < $endSchedule) {
                return true;
            }
        }

        return false;
    }
}
