<?php

namespace App\Http\Controllers\Lab;

use App\ExamPackage;
use App\Http\Controllers\Controller;
use App\Product;


class ExamsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $search['q'] = request('q');

        $examsProducts = Product::query()
            ->search($search['q'])
            ->where('laboratory', 1)
            ->where('office_id', auth()->user()->offices->first()?->id) // gps laboratorio
            ->paginate();

        return view('lab.exams.index', [
            'exams' => $examsProducts,
            'search' => $search
        ]);
    }

    public function create()
    {

        return view('lab.exams.create');
    }

    public function edit(Product $exam)
    {
        $exam->load('taxes');
        return view('lab.exams.edit', compact('exam'));
    }
}
