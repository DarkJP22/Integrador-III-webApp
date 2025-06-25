<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class UserDiscountHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    
    public function index(User $user)
    {

        $data = [
            'total' => $user->discountHistories()->sum("total_discount")
        ];

        return $data;
    }
    

}
