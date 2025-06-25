<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Nopathological;
class NopathologicalController extends Controller
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

        return Nopathological::create($data)->load('user');

    }
    public function destroy($id)
    {

        $pathological = Nopathological::findOrFail($id)->delete();


        return '';

    }
}
