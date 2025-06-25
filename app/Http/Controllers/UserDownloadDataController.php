<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserDownloadDataController extends Controller
{
    public function show(User $user)
    {
        return view('user.download-data', compact('user'));
    }

    public function download(User $user)
    {
        return response()->download(storage_path("app/downloads-users/data_{$user->id}.zip"));
    }
}
