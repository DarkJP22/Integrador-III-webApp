<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Opevaluation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstheticEvaluationController extends Controller
{
    public function __construct() {
        
        $this->middleware('auth');

    }

    public function index()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $search['q'] = request('q');

        $evaluations = Opevaluation::search($search)->paginate(10);

        return view('esthetic.evaluations.index', compact('evaluations', 'search'));
    }

    public function create()
    {
         if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }


      return view('esthetic.evaluations.create');
        
    }

    public function edit(Opevaluation $evaluation)
    {
         if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }


        return view('esthetic.evaluations.edit', compact('evaluation'));
    }

    public function store()
    {
        $data = $this->validate(request(), [
            'category' => ['required'],
            'name' => ['required', Rule::unique('opevaluations', 'name')->where(function ($query) {
                return $query->where('category', request('category'));
            })]
        ]);
        
        $office = auth()->user()->offices->first();
        $data['office_id'] = $office->id;

        Opevaluation::create($data);

        return redirect('/clinic/esthetic/evaluations');

    }

    public function update(Opevaluation $evaluation)
    {
        $data = $this->validate(request(), [
            'category' => ['required'],
            'name' => ['required', Rule::unique('opevaluations', 'name')->ignore($evaluation->id)->where(function ($query) {
                return $query->where('category', request('category'));
            })]
        ]);
        
        $evaluation->update($data);

        return redirect('/clinic/esthetic/evaluations');

    }

    public function destroy(Opevaluation $evaluation)
    {

        $evaluation->delete();

        return redirect('/clinic/esthetic/evaluations');

    }
}
