<?php

namespace App\Http\Controllers\Pharmacy;

use Illuminate\Http\Request;
use App\Media;
use App\Http\Controllers\Controller;
use App\MediaTag;


class MediaController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {
        $tags = MediaTag::orderBy('name', 'ASC')->pluck('name')->all();


        return view('pharmacy.media.index', compact('tags'));
    }

    public function media()
    {
        $search['q'] = request('q');
        $search['tags'] = request('tags');

        $media = Media::search($search)->tags($search['tags'])->paginate(10);

        if (request()->wantsJson()) {
            return response($media, 200);
        }


        return $media;
    }

    public function store()
    {
        $validated = request()->validate([
            'title' => 'required',
            'description' => 'nullable',
            'url' => 'required',
            'tags' => 'nullable'
        ]);

        $pharmacy = auth()->user()->pharmacies->first();

        $validated['pharmacy_id'] = $pharmacy->id;
        $validated['uploaded_by'] = auth()->id();

        $media = Media::create($validated);

        if (request()->wantsJson()) {
            return response($media, 201);
        }

        return $media;
    }

    public function update(Media $media)
    {
        $validated = request()->validate([
            'title' => 'required',
            'description' => 'nullable',
            'url' => 'required',
            'tags' => 'nullable'
        ]);

        $media->fill($validated);
        $media->save();

        if (request()->wantsJson()) {
            return response($media, 200);
        }

    }

    public function destroy(Media $media)
    {
        $result = $media->delete();


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'Error al eliminar'], 422);

        }

    }
}
