<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $office = auth()->user()->offices->first();

        return $office->settings;
    }

    public function store()
    {
        $data = request()->validate([
            'lab_exam_cash_discount' => ['sometimes', 'numeric'],
            'lab_whatsapp_exam_message' => ['sometimes', 'string'],
            'lab_whatsapp_package_exam_message' => ['sometimes', 'string'],
        ]);

        $office = auth()->user()->offices->first();

        if($office->settings === null) {
            $office->settings = [];
        }

        collect($data)->keys()->each(function($key) use ($data, $office) {
            $office->settings[$key] = $data[$key];
        });

        $office->save();

        return $office->settings;
    }
}
