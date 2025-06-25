<?php

namespace App\Http\Controllers\Api\Medic;

use App\Patient;
use App\Http\Controllers\Controller;
use App\Labresult;
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

        $patient->labresults()->where('medic_id', auth()->user()->id)->update(['read_at' => now()]);
        
        $labresults = $patient->labresults()->where('medic_id', auth()->user()->id)->filter(request(['q']))->latest()->paginate(10);


        return response($labresults, 200);

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
            $data['medic_id'] = auth()->user()->id;

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

   
}
