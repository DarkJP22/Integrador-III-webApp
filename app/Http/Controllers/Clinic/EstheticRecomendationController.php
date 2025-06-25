<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Oprecomendation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstheticRecomendationController extends Controller
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

        $recomendations = Oprecomendation::search($search)->paginate(10);

        return view('esthetic.recomendations.index', compact('recomendations', 'search'));
    }

    public function create()
    {
         if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }


      return view('esthetic.recomendations.create');
        
    }

    public function edit(Oprecomendation $recomendation)
    {
         if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }


        return view('esthetic.recomendations.edit', compact('recomendation'));
    }

    public function store()
    {
        $data = $this->validate(request(), [
            'category' => ['required'],
            'name' => ['required', Rule::unique('oprecomendations', 'name')->where(function ($query) {
                return $query->where('category', request('category'));
            })]
        ]);

        $office = auth()->user()->offices->first();
        $data['office_id'] = $office->id;
        
        Oprecomendation::create($data);

        return redirect('/clinic/esthetic/recomendations');

    }

    public function update(Oprecomendation $recomendation)
    {
        $data = $this->validate(request(), [
            'category' => ['required'],
            'name' => ['required', Rule::unique('oprecomendations', 'name')->ignore($recomendation->id)->where(function ($query) {
                return $query->where('category', request('category'));
            })]
        ]);
        
        $recomendation->update($data);

        return redirect('/clinic/esthetic/recomendations');

    }

    public function destroy(Oprecomendation $recomendation)
    {

        $recomendation->delete();

        return redirect('/clinic/esthetic/recomendations');

    }
}
