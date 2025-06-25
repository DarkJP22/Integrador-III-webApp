<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Illuminate\Support\Arr;

class ProductController extends Controller
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
        
        $officeIds = auth()->user()->isAssistant() ? auth()->user()->clinicsAssistants->pluck('id') : auth()->user()->offices->pluck('id'); 
        $products = Product::whereIn('office_id', $officeIds);

        if (request('code')) {
            $product = $products->where('code', request('code'))->with('taxes')->first();
            if (request()->wantsJson()) {
                return response($product, 200);
            }
        }
        
        if (request('q')) {
            $products = $products->search(request('q'))->with('taxes')->latest()->paginate(10);
        } else {
            $products = $products->latest()->with('taxes')->paginate(10);
        }

        if (request()->wantsJson()) {
            return response($products, 200);
        }

        return view('products.index', compact('products', 'search'));
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
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',

        ]);

        $data = $this->prepareData(request()->all());

        $product = Product::create($data);

        if (request()->wantsJson()) {
            return response($product, 201);
        }

  
    }


    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);


        $this->validate(request(), [
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

        $data = $this->prepareData(request()->all());

        $product->fill($data);
        $product->save();

        if (isset($data['taxes'])) {
            $product->taxes()->sync($data['taxes']);
        }else{
            $product->taxes()->sync([]);
        }
        
        $product->calculatePriceWithTaxes();

        if (request()->wantsJson()) {
            return response($product, 201);
        }

        return Redirect('/products');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $this->authorize('update', $product);

        $product->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return Redirect('/products');
    }

    public function prepareData($data)
    {
        
        if (empty($data['taxes'])) {
            $data = Arr::except($data, ['taxes']);
        }

        return $data;
    }
}
