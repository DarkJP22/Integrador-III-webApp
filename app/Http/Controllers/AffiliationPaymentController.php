<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Affiliation;
use App\AffiliationPayment;

class AffiliationPaymentController extends Controller
{
    
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
      
       
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Affiliation $affiliation)
    {
       
        
        return $affiliation->payments()->with('transaction')->paginate(10);
       
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Affiliation $affiliation)
    {
        $validated = request()->validate([
            'detail' => 'required',
            'date' => 'required',
            'amount' => 'required|numeric',
            'payment_way' => 'nullable',
            'code_transaction' => 'nullable',

           
        ]);
        
    

        
        $payment = $affiliation->payments()->create($validated);
        

        if (request()->wantsJson()) {
            return response($payment, 201);
        }
      
       
    
        return redirect('/affiliations');

    }

   

    public function update(Affiliation $affiliation, AffiliationPayment $payment)
    {
        $validated = request()->validate([
            'detail' => 'required',
            'date' => 'required',
            'amount' => 'required|numeric',
            'payment_way' => 'nullable',
            'code_transaction' => 'nullable',

           
        ]);

        

       
        $payment->fill($validated);
        $payment->save();

       
        if (request()->wantsJson()) {
            return response($payment, 200);
        }
       

        return redirect('/affiliations');
       
    }

     /**
     * Eliminar consulta(cita)
     */
    public function destroy(Affiliation $affiliation, AffiliationPayment $payment)
    {
        $result = $payment->delete();


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'Error al eliminar'], 422);
        }

        return redirect('/affiliations');
    }


  
    
}
