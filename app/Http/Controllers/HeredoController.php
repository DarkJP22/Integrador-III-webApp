<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Heredo;

class HeredoController extends Controller
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

        return Heredo::create($data)->load('user');

    }
    public function destroy($id)
    {

        $pathological = Heredo::findOrFail($id)->delete();


        return '';

    }
}
