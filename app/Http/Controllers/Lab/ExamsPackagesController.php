<?php

namespace App\Http\Controllers\Lab;

use App\ExamPackage;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ExamsPackagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $search['q'] = request('q');

        $examPackages = ExamPackage::query()
            ->search($search)
            ->where('office_id', auth()->user()->offices->first()?->id)
            ->paginate();

        return view('lab.examsPackages.index', [
            'examPackages' => $examPackages,
            'search' => $search
        ]);
    }

    public function edit(ExamPackage $examPackage)
    {

        return view('lab.examsPackages.edit', compact('examPackage'));
    }

    public function store()
    {

        $data = $this->validate(request(), [
            'name' => ['required'],
            'photo' => ['sometimes', 'nullable', 'file'],
        ]);

        $package = DB::transaction(function () use ($data) {

            $package = ExamPackage::create([
                ...Arr::except($data, ['photo']),
                'office_id' => auth()->user()->offices->first()?->id
            ]);

            if (isset($data['photo'])) {
                $package->updatePhoto($data['photo']);
            }

            return $package;
        });

        flash('Paquete Creado', 'success');

        return redirect('/lab/exams-packages');
    }

    public function create()
    {

        return view('lab.examsPackages.create');
    }

    public function update(ExamPackage $examPackage)
    {
  
        $data = $this->validate(request(), [
            'name' => ['sometimes', 'required'],
            'photo' => ['sometimes', 'nullable', 'file'],

        ]);

        DB::transaction(function () use ($data, $examPackage) {

            if (isset($data['photo'])) {
                $examPackage->updatePhoto($data['photo']);
            }

            $examPackage->fill([
                ...Arr::except($data, ['photo']),
            ])->save();
        });

        flash('Paquete actualizado', 'success');

        return redirect('/lab/exams-packages');
    }

    public function destroy(ExamPackage $examPackage)
    {

        $examPackage->delete();

        flash('Paquete eliminado', 'success');

        return redirect('/lab/exams-packages');
    }
}
