<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PhysicalExam;

class PhysicalExamController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function update($id)
    {

        $physicalExam = PhysicalExam::findOrFail($id);

        $physicalExam->fill(request()->all());
        $physicalExam->save();

        return '';
    }
}
