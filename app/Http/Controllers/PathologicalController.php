<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pathological;
class PathologicalController extends Controller
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

        return Pathological::create($data)->load('user');

    }
    public function destroy($id)
    {

        $pathological = Pathological::findOrFail($id)->delete();


        return '';

    }
}
