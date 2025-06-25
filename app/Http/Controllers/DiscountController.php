<?php

namespace App\Http\Controllers;

use App\Enums\OfficeType;
use Illuminate\Http\Request;
use App\Discount;
use App\User;
use App\Office;

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

        if(request('office')){
            $office = Office::find(request('office'));

            if ($office && $office->type === OfficeType::MEDIC_OFFICE) {
                // $assistantUser = \DB::table('assistants_users')->where('assistant_id', auth()->user()->id)->first();
                $user = $office->users()->whereHas('roles', function ($q) {
                    $q->where('name', 'medico');
                })->first();

            } else {

                if(auth()->user()->isLab()){
                    $user = $office->users()->whereHas('roles', function ($q)
                    {
                       $q->where('name','laboratorio');
                    })->first();
                }else{

                    $user = $office->users()->whereHas('roles', function ($q)
                    {
                       $q->where('name','clinica');
                    })->first();
                }
               
            }
        }else{

            if (auth()->user()->isAssistant()) {
                $assistantUser = \DB::table('assistants_users')->where('assistant_id', auth()->user()->id)->first();

                $user = User::find($assistantUser->user_id);

            } else {
                $user = auth()->user();
            }


        }

       

        if (request('q')) {
            $discounts = Discount::where('user_id', $user->id)->search(request('q'))->latest()->paginate(10);
        } else {
            $discounts = Discount::where('user_id', $user->id)->latest()->paginate(10);
        }

        if (request()->wantsJson()) {
            return response($discounts, 200);
        }


        return view('discounts.index', compact('discounts', 'search'));
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

        ]);
        $data = request()->all();

        $data['user_id'] = auth()->user()->id;

        $discount = Discount::create($data);

        if (request()->wantsJson()) {
            return response($discount, 201);
        }

        flash('Descuento creado', 'success');

        return redirect('/discounts');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Discount::class);

        return view('discounts.create');
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

        return view('discounts.edit', compact('discount'));
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
        ]);

        $discount->fill(request()->all());
        $discount->save();

        if (request()->wantsJson()) {
            return response($discount, 200);
        }

        flash('Descuento actualizado', 'success');

        return Redirect('/discounts');
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

        return Redirect('/discounts');
    }
}
