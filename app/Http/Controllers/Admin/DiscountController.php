<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Discount;
use App\Http\Controllers\Controller;

class DiscountController extends Controller
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
        
        $search['q'] = request('q');

        if (request('q')) {
            $discounts = Discount::where('for_gps_users', 1)->search(request('q'))->latest()->paginate(10);
        } else {
            $discounts = Discount::where('for_gps_users', 1)->latest()->paginate(10);
        }

        if (request()->wantsJson()) {
            return response($discounts, 200);
        }


        return view('admin.discounts.index', compact('discounts', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Discount::class);

        return view('admin.discounts.create');
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
            'name' => 'required|string|max:255',
            'tarifa' => 'required|numeric',
            'to' => 'required'
        ]);
        $data = request()->all();

        $data['user_id'] = auth()->user()->id;
        $data['for_gps_users'] = 1;

        $discount = Discount::create($data);

        if (request()->wantsJson()) {
            return response($discount, 201);
        }

        flash('Descuento creado', 'success');

        return redirect('/admin/discounts');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Discount $discount)
    {
        $this->authorize('update', $discount);

        return view('admin.discounts.edit', compact('discount'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Discount $discount)
    {
        $this->authorize('update', $discount);

        $this->validate(request(), [
            'name' => 'required|string|max:255',
            'tarifa' => 'required|numeric',
            'to' => 'required'
        ]);

        $discount->fill(request()->all());
        $discount->save();

        if (request()->wantsJson()) {
            return response($discount, 200);
        }

        flash('Descuento actualizado', 'success');

        return Redirect('/admin/discounts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Discount $discount)
    {
        $this->authorize('update', $discount);

        $discount->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return Redirect('/admin/discounts');
    }
}
