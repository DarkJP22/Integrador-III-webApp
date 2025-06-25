<?php

namespace App\Http\Controllers;

use App\Accumulated;
use App\Http\Controllers\Controller;


class AccumulatedsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    
    public function show(Accumulated $accumulated)
    {
        return $accumulated->load(['user', 'holder', 'patients', 'transactions' => function ($query) {
            $query->where('action', 'not like','Rollback%')
                ->orderBy('created_at', 'desc')->limit(10);
        }, 'transactions.resource']);
    }

  

    

}
