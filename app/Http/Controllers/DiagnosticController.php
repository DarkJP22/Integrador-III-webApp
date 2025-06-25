<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Diagnostic;

class DiagnosticController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
    }

    public function store()
    {

        $diagnostic = Diagnostic::create(request()->all());


        return $diagnostic;

    }
    public function destroy($id)
    {

        $diagnostic = Diagnostic::findOrFail($id)->delete();


        return '';

    }
}
