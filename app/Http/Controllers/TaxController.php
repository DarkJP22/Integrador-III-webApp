<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tax;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$this->authorize('create', Tax::class);
     
        $search['q'] = request('q');

        if (request('q')) {
            $taxes = Tax::search(request('q'))->latest()->paginate(10);
        } else {
            $taxes = Tax::latest()->paginate(10);
        }

        if (request()->wantsJson()) {
            return response($taxes, 200);
        }


        return view('admin.taxes.index', compact('taxes', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Tax::class);

        return view('admin.taxes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'code' => 'required',
            'name' => 'required|string|max:255',
            'tarifa' => 'required|numeric',
            'CodigoTarifa'=> 'required'
            
        ]);


        $tax = Tax::create(request()->all());

        if (request()->wantsJson()) {
            return response($tax, 201);
        }

        flash('Impuesto creado', 'success');

        return redirect('/taxes');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Tax $tax)
    {
        $this->authorize('update', $tax);

        return view('admin.taxes.edit', compact('tax'));
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Tax $tax)
    {
        $this->authorize('update', $tax);

        return view('admin.taxes.edit', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tax $tax)
    {
        $this->authorize('update', $tax);

        $this->validate(request(), [
            'code' => 'required',
            'name' => 'required|string|max:255',
            'tarifa' => 'required|numeric',
            'CodigoTarifa'=> 'required'
        ]);

        $tax->fill(request()->all());
        $tax->save();

        if (request()->wantsJson()) {
            return response($tax, 200);
        }

        flash('Impuesto actualizado', 'success');

        return Redirect('/taxes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tax $tax)
    {
        $this->authorize('update', $tax);

        $tax->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return Redirect('/taxes');
    }

}
