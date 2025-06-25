<?php

namespace App\Http\Controllers\Api;

use App\ExamPackage;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class LabExamsPackagesController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $examsPackages = ExamPackage::query()
            ->search($request->input('q'))
            ->where('office_id', $request->input('office_id')) // gps laboratorio
            ->paginate();

        return $examsPackages;
    }
}
