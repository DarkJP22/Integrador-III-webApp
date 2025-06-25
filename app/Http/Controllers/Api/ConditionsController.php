<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\MediaTag;

class ConditionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    
    public function index()
    {

        $tags = MediaTag::pluck('name')->all();

        return $tags;
    }

  

    

}
