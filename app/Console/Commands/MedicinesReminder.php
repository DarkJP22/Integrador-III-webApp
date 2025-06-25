<?php

namespace App\Console\Commands;

use App\Enums\MedicineReminderStatus;
use App\Enums\MedicineReminderStatusNotification;
use Illuminate\Console\Command;
use App\MedicineReminder;
use Carbon\Carbon;

//use App\Notifications\AppointmentReminder as ReminderNotification;
use App\Medicine;
use Illuminate\Support\Facades\Log;

class MedicinesReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:medicinesReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recordatorio de compra de medicamentos en farmacia';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $reminders = Medicine::with('patient')->where('remember', 1)->where('creator_type', 'App\Pharmacy')->get();
        $countNotification = 0;


        MedicineReminder::query()
            ->where('status_notification', MedicineReminderStatusNotification::SENT)
            ->where('date', '<', today())
            ->update(['status_notification' => MedicineReminderStatusNotification::DID_NOT_ANSWER]);

        foreach ($reminders as $reminder) {

            if ($reminder->date_purchase &&
                $reminder->reminder_start &&
                ($reminder->active_remember_for_days === -1 ||
                    (today()->greaterThanOrEqualTo($reminder->reminder_start) && today()->lessThanOrEqualTo($reminder->reminder_end))
                )
            ) {


                if ((int) Carbon::now()->diffInDays($reminder->date_purchase) == 0) {


                    $medicineReminder = MedicineReminder::create([
                        'pharmacy_id' => $reminder->creator_id,
                        'medicine_id' => $reminder->id,
                        'name' => $reminder->name,
                        'date' => $reminder->date_purchase,
                        'status_notification' => MedicineReminderStatusNotification::NOT_SENT,
                        'status' => MedicineReminderStatus::NO_CONTACTED,

                    ]);

                    $this->addPatientToReminder($medicineReminder, $reminder);

                    $nextDatePurchase = Carbon::parse($reminder->date_purchase)->addDays($reminder->remember_days);
                    $dateTreatmentEnd = Carbon::parse($reminder->date_purchase)->addDays($reminder->active_remember_for_days);

                    if ($reminder->remember_days &&
                        $reminder->remember_days !== 1 &&
                        ($reminder->active_remember_for_days === -1 || $nextDatePurchase->lessThanOrEqualTo($dateTreatmentEnd))
                    ) {
                        $reminder->date_purchase = $nextDatePurchase->toDateString();
                    }

//                    if ($reminder->remember_days) {
//
//                        $reminder->date_purchase = Carbon::parse($reminder->date_purchase)->addDays($reminder->remember_days)->toDateString();
//
//
//                    }

                    $reminder->save();


                    $countNotification++;


                }
                //\Log::info('diff in days '.Carbon::now()->diffInDays($reminder->date_purchase));


            }

        }
        //\Log::info($countNotification . ' recordatorios de medicamentos creados');
        $this->info('Hecho, '.$countNotification.' recordatorios de medicamentos creados');
    }

    /**
     * @param  MedicineReminder  $medicineReminder
     * @param  mixed  $reminder
     * @return void
     */
    public function addPatientToReminder(MedicineReminder $medicineReminder, mixed $reminder): void
    {
        if (!$medicineReminder->patients()->where('patients.id', $reminder->patient?->id)->exists()) {
            $medicineReminder->patients()->save($reminder->patient);
        }
    }
}
