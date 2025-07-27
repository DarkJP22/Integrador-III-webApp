<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Pharmacy;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    /**
     * Display a listing of all pharmacies.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search['q'] = request('q');
        
        $pharmacies = Pharmacy::when($search['q'], function($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
        })->get();

        return response()->json([
            'pharmacies' => $pharmacies,
            'search' => $search
        ]);
    }
}
