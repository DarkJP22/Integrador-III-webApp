<?php

namespace App\Actions;

use App\Appointment;
use App\AppointmentRequest;
use App\Enums\AppointmentRequestStatus;
use App\Enums\AppointmentStatus;
use App\Plan;
use App\User;
use Illuminate\Support\Facades\DB;

class GetAppointmentAndRequestsForCommission
{

    public function handle(User $user, Plan $plan, $billing_date_from, $billing_date_to): object
    {
        $scheduledAppointmentRequestsInRangeQuantity = AppointmentRequest::query()
            ->whereBetween('date', [$billing_date_from, $billing_date_to])
            ->where('medic_id', $user->id)
            ->where('status', AppointmentRequestStatus::SCHEDULED)
            ->byDiffInMinutes('created_at', 'scheduled_at', '<=', $plan->commission_discount_range_in_minutes)
            //->whereRaw('ABS(TIMESTAMPDIFF(MINUTE, created_at, scheduled_at)) <= ?', $plan->commission_discount_range_in_minutes)
            ->whereHas('user', function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->where('name', 'paciente');
                });
            })
            ->count();

        $scheduledAppointmentRequestsOutRangeQuantity = AppointmentRequest::query()
            ->whereBetween('date', [$billing_date_from, $billing_date_to])
            ->where('medic_id', $user->id)
            ->where('status', AppointmentRequestStatus::SCHEDULED)
            ->byDiffInMinutes('created_at', 'scheduled_at', '>', $plan->commission_discount_range_in_minutes)
            //->whereRaw('ABS(TIMESTAMPDIFF(MINUTE, created_at, scheduled_at)) > ?', $plan->commission_discount_range_in_minutes)
            ->whereHas('user', function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->where('name', 'paciente');
                });
            })
            ->count();


        $pendingAppointmentRequestsQuantity = AppointmentRequest::query()
            ->whereBetween('date', [$billing_date_from, $billing_date_to])
            ->where('medic_id', $user->id)
            ->whereIn('status', [AppointmentRequestStatus::PENDING, AppointmentRequestStatus::RESERVED])
            ->whereHas('user', function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->where('name', 'paciente');
                });
            })
            ->count();

        $scheduledAppointmentsQuantity = Appointment::query()
            ->whereBetween('date', [$billing_date_from, $billing_date_to])
            ->where('user_id', $user->id)
            ->whereIn('status', [AppointmentStatus::SCHEDULED, AppointmentStatus::STARTED])
            ->whereHas('creator', function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->where('name', 'paciente');
                });
            })
            ->count();

        $commission_discount = ($plan->commission_discount / 100);
        $cost_by_appointment = $user->specialities->count() ? $plan->specialist_cost_commission_by_appointment : $plan->general_cost_commission_by_appointment;
        $commission_discount_val = ($cost_by_appointment * $commission_discount);
        $cost_by_appointment_with_discount = $cost_by_appointment - $commission_discount_val;

        return (object)[
            'scheduledAppointmentRequestsInRangeQuantity' => $scheduledAppointmentRequestsInRangeQuantity,
            'scheduledAppointmentRequestsOutRangeQuantity' => $scheduledAppointmentRequestsOutRangeQuantity,
            'pendingAppointmentRequestsQuantity' => $pendingAppointmentRequestsQuantity,
            'scheduledAppointmentsQuantity' => $scheduledAppointmentsQuantity,
            'commission_discount' => $commission_discount,
            'cost_by_appointment' => $cost_by_appointment,
            'commission_discount_val' => $commission_discount_val,
            'cost_by_appointment_with_discount' => $cost_by_appointment_with_discount,
        ];
    }

}