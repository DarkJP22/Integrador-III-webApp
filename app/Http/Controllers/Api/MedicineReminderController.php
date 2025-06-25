<?php

namespace App\Http\Controllers\Api;

use App\Enums\MedicineReminderStatus;
use App\Http\Controllers\Controller;
use App\MedicineReminder;
use Illuminate\Validation\Rule;


class MedicineReminderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function update(MedicineReminder $reminder)
    {
        $data = request()->validate([
            'status' => ['sometimes', 'required', Rule::enum(MedicineReminderStatus::class)]
        ]);

        $reminder->update($data);

        return $reminder;
    }
}
