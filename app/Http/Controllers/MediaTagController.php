<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MediaTag;

class MediaTagController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('create', MediaTag::class);
        
        $search['q'] = request('q');

        $tags = MediaTag::search($search)->paginate(10);

        if (request()->wantsJson()) {
            return response($tags, 200);
        }

        return view('admin.mediatags.index',compact('tags','search'));
    }

    public function tags()
    {
        $search['q'] = request('q');

        $tags = MediaTag::search($search)->pluck('name')->all();

        if (request()->wantsJson()) {
            return response($tags, 200);
        }

      
    }

    /**
     * Mostrar vista crear paciente
     */
    public function create()
    {
        $this->authorize('create', MediaTag::class);

        return view('admin.mediatags.create');

    }


    public function store()
    {
        $validated = request()->validate([
            'name' => 'required',
          
        ]);

       
        $tag = MediaTag::create($validated);

        if (request()->wantsJson()) {
            return response($tag, 201);
        }

        return redirect('/mediatags');
    }

    /**
     * Mostrar vista editar paciente
     */
    public function edit(MediaTag $tag)
    {
        $this->authorize('update', $tag);

        return view('admin.mediatags.edit', compact('tag'));
    }

    public function update(MediaTag $tag)
    {
        $validated = request()->validate([
            'name' => 'required',
           
        ]);

        $tag->fill($validated);
        $tag->save();

        if (request()->wantsJson()) {
            return response( $tag, 200);
        }

        return redirect('/mediatags');
    }

    public function destroy(MediaTag $tag)
    {
        $result = $tag->delete();


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'Error al eliminar'], 422);
        }

        return redirect('/mediatags');
    }
}
