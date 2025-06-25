<?php

namespace App\Http\Controllers;

use App\Drug;
use App\Enums\DrugStatus;
use Illuminate\Http\Request;

class DrugController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
       // $this->authorize('viewAny', Drug::class);



        $drugs = Drug::search(request(['q']))
            ->when(request('sort'), function ($query, $sort) {
                return $query->orderBy($sort, request('order', 'asc'));
            })
            ->latest()->paginate(10);

        if (request()->wantsJson()) {
            return response($drugs, 200);
        }


        return view('admin.drugs.index', compact('drugs'));
    }

    public function store(Request $request)
    {
        $data = $this->validate(request(), [
            'name' => ['required'],
            'description' => ['nullable'],
            'presentation' => ['nullable'],
            'laboratory' => ['nullable'],
            'active_principle' => ['nullable'],
            'image_path' => ['nullable', 'image', 'max:2048'],
            'status' => ['required'],
        ]);

        if (request()->hasFile('image_path')) {
            $path = request()->file('image_path')->store('drugs', 's3');
            $data['image_path'] = $path;
        }

        $drug = Drug::create([
            ...$data,
            'creator_id' => auth()->id(),
        ]);


        return redirect('/drugs');
    }

    public function create()
    {
        $this->authorize('create', Drug::class);

        return view('admin.drugs.create',[
            'statuses' => DrugStatus::options(),
        ]);
    }

    public function edit(Drug $drug)
    {
        $this->authorize('update', $drug);

        return view('admin.drugs.edit',[
            'drug' => $drug,
            'statuses' => DrugStatus::options(),
        ]);
    }

    public function update(Request $request, Drug $drug)
    {
        $this->authorize('update', $drug);

        $data = $this->validate(request(), [
            'name' => ['required'],
            'description' => ['nullable'],
            'presentation' => ['nullable'],
            'laboratory' => ['nullable'],
            'active_principle' => ['nullable'],
            'image_path' => ['nullable', 'image', 'max:2048'],
            'status' => ['required'],
        ]);

        if (request()->hasFile('image_path')) {
            $path = request()->file('image_path')->store('drugs', 's3');
            $data['image_path'] = $path;
        }else{
            $data['image_path'] = $drug->image_path;
        }

        $drug->update($data);

        return redirect('/drugs');
    }

    public function destroy(Drug $drug)
    {
        $this->authorize('delete', $drug);

        $drug->delete();

        return redirect('/drugs');
    }
}
