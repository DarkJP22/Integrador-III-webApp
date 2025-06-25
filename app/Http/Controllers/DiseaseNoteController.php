<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DiseaseNote;

class DiseaseNoteController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
      
    }
    public function update($id)
    {

        $diseaseNote = DiseaseNote::findOrFail($id);

        $diseaseNote->fill(request()->all());
        $diseaseNote->save();

        return '';
    }
}
