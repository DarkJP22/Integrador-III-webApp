<?php

namespace App\Http\Controllers\Api;

use App\Labresult;
use App\Patient;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PatientLabresultsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(Patient $patient)
    {
        
        $labresults = $patient->labresults()->with('medic')->filter(request(['q']))->latest()->paginate(10);

        if (request()->wantsJson()) {
            return response($labresults, 200);
        }
    }

    public function store(Patient $patient)
    {
        $data = $this->validate(request(), [
            'date' => ['required'],
            'description' => ['sometimes', 'nullable'],
            'file' => 'nullable|max:5000|mimes:jpg,jpeg,bmp,png,pdf,xls,xlsx,doc,docx',
        ]);

        return DB::transaction(function () use($patient, $data)
        {
            $data['patient_id'] = $patient->id;
            $data['user_id'] = auth()->user()->id;

            if (request()->file('file')) {

                $file = request()->file('file');
                $name = $file->getClientOriginalName();
                $ext = $file->guessClientExtension();
                $onlyName = Str::slug(pathinfo($name)['filename'], '-');
                $data['name'] = $onlyName . '.' . $ext;

                $labresult = Labresult::create($data);

                $file->storeAs('patients/' . $patient->id . '/labresults/' . $labresult->id, $onlyName . '.' . $ext, 's3');

                return $labresult;

            }else{
                $data['name'] = '';
                $labresult = Labresult::create($data);

                return $labresult;
            }


        });

    }

    public function destroy(Patient $patient, Labresult $labresult)
    {

        $labresult->delete();

        $urlResource = $labresult->url;

        if($urlResource){
            \Storage::disk('s3')->delete('patients/' . $patient->id . '/labresults/' . $labresult->id . '/' . $labresult->name);
        }

        return response()->json(null, 204);

    }

   
}
