<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gineco;

class GinecoController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');

    }

    public function store()
    {
        request()->validate([
            'name' => 'required',
        ]);
        
        $data = request()->all();

        $data['user_id'] = auth()->id();

        return Gineco::create($data)->load('user');

    }
    public function destroy($id)
    {

        $pathological = Gineco::findOrFail($id)->delete();


        return '';

    }
}
