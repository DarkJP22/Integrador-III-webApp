<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Affiliation;


class AffiliationTransactionController extends Controller
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
       


        
        return $affiliation->transactions()->where('transactable_type', 'App\Invoice')->with('transactable')->paginate(10);
       
    }

    

   
  
    
}
