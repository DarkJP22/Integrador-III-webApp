<?php

namespace App\Http\Controllers\Api;



use App\Http\Controllers\Controller;
use App\Setting;


class ConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {

        $config = [];

        return $config;
    }
}
