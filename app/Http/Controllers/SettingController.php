<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class SettingController extends Controller
{
    
    public function update(User $user)
    {

        $user->setSettings(request()->all());

        return $user->getAllSettings();
    }
}
