<?php

namespace App\Http\Controllers\Lab;


use App\Http\Controllers\Controller;
use App\LabVisit;
use App\Office;
use Illuminate\Validation\Rule;

class VisitsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        if (request()->wantsJson()) {

            $visits = LabVisit::search(request('q'))
                ->where('office_id', auth()->user()->offices->first()?->id)
                ->paginate();

            return response($visits, 200);
        }

        return view('lab.visits.index', []);
    }

    public function store(): LabVisit
    {
        $data = request()->validate([
            'location' => ['required', Rule::unique('lab_visits')->where(fn($query) => $query->where('office_id', auth()->user()->offices->first()?->id))],
            'schedule' => ['required', 'array']
        ]);

        $labVisit = LabVisit::create([
            ...$data,
            'office_id' => auth()->user()->offices->first()?->id
        ]);

        return $labVisit;
    }

    public function destroy(LabVisit $labVisit)
    {


        $labVisit->delete();

        return 'ok';
    }

    public function settings()
    {
        $office = auth()->user()->offices->first();

        $office->settings['visit_location'] = request('visit_location');
        $office->settings['otro'] = 'bar';
        $office->settings['cast'] = 2;

        $office->save();

        return $office->settings;
    }

    public function update(LabVisit $labVisit): LabVisit
    {
        $data = request()->validate([
            'location' => ['required', Rule::unique('lab_visits')->ignore($labVisit->id)],
            'schedule' => ['required', 'array']
        ]);

        $labVisit->fill($data)->save();

        return $labVisit->fresh();
    }
}
