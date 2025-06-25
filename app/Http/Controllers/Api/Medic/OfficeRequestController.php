<?php

namespace App\Http\Controllers\Api\Medic;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewRequestClinic;
use App\Http\Controllers\Controller;

class OfficeRequestController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        $this->administrators = User::whereHas('roles', function ($query) {
            $query->where('name', 'administrador');
        })->get();

    }


    public function store()
    {

        $this->validate(request(), [
            'name' => 'required',
        ]);

        $requestOffice = auth()->user()->requestOffices()->create(request()->all());

        Notification::send($this->administrators, new NewRequestClinic($requestOffice));
       
        return $requestOffice;
    }
}
