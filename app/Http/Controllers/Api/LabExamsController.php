<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class LabExamsController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(Request $request)
    {

        $examsProducts = Product::query()
            ->search($request->input('q'))
            ->where('laboratory', 1)
            ->where('office_id', $request->input('office_id')) // gps laboratorio
            ->paginate();

        return $examsProducts;
    }
}
