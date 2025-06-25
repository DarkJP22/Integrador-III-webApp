<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Optreatment;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstheticTreatmentController extends Controller
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

        $treatments = Optreatment::search($search)->paginate(10);

        return view('esthetic.treatments.index', compact('treatments', 'search'));
    }

    public function create()
    {
         if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }


      return view('esthetic.treatments.create',[
          'treatment' => new Optreatment,
          'selectedProduct' => false
      ]);
        
    }

    public function edit(Optreatment $treatment)
    {
         if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $selectedProduct = Product::find($treatment->product_id)?->load('taxes');

        return view('esthetic.treatments.edit', compact('treatment', 'selectedProduct'));
    }

    public function store()
    {
        $data = $this->validate(request(), [
            'category' => ['required'],
            'name' => ['required', Rule::unique('optreatments', 'name')->where(function ($query) {
                return $query->where('category', request('category'));
            })],
            'CodigoMoneda' => ['sometimes'],
            'product_id' => ['sometimes'],
            'price' => ['numeric'],
            'discount' => ['numeric'],
            'tax' => ['numeric'],
        ]);

        $office = auth()->user()->offices->first();
        $data['office_id'] = $office->id;
        
        $treatment = Optreatment::create($data);

        if (request()->wantsJson()) {
            return response($treatment, 201);
        }

        return redirect('/clinic/esthetic/treatments');

    }

    public function update(Optreatment $treatment)
    {
        $data = $this->validate(request(), [
            'category' => ['required'],
            'name' => ['required', Rule::unique('optreatments', 'name')->ignore($treatment->id)->where(function ($query) {
                return $query->where('category', request('category'));
            })],
            'CodigoMoneda' => ['sometimes'],
            'product_id' => ['sometimes'],
            'price' => ['numeric'],
            'discount' => ['numeric'],
            'tax' => ['numeric'],
        ]);
        
        $treatment->update($data);

        if (request()->wantsJson()) {
            return response($treatment, 200);
        }

        return redirect('/clinic/esthetic/treatments');

    }

    public function destroy(Optreatment $treatment)
    {

        $treatment->delete();

        return redirect('/clinic/esthetic/treatments');

    }
}
