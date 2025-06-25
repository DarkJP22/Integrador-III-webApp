<?php

namespace App\Http\Controllers;

use App\Imports\DrugsImport;
use Illuminate\Http\Request;

class DrugImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store()
    {
        $this->validate(request(), [
            'file' => 'required|file|mimes:xlsx',
        ]);

        $file = request()->file('file');

        (new DrugsImport(request()->user()))->import($file, 'local', \Maatwebsite\Excel\Excel::XLSX)->chain([
            new \App\Jobs\NotifyUserOfCompletedImport(request()->user()),
        ]);



        return back();

    }

}
