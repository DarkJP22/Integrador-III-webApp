<?php

namespace App\Http\Controllers\Beautician;

use App\Appointment;
use App\Documentation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentationController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
      
    }
    public function store(Appointment $appointment)
    {
      
        $data = $this->validate(request(), [
            'date' => 'required',
            'file' => 'nullable|max:5000|mimes:jpg,jpeg,bmp,png',
        ]);
    

        $mimes = ['jpg', 'jpeg', 'bmp', 'png'];
        $path = 'error';

        if (request()->file('file')) {
           
            $file = request()->file('file');
            $name = $file->getClientOriginalName();
            $ext = $file->guessClientExtension();
            $onlyName = Str::slug(pathinfo($name)['filename'], '-');
            $data['url'] = $name;

            if (in_array($ext, $mimes)) {

                $data['patient_id'] = $appointment->patient_id;
                $documentation = $appointment->documentations()->create($data);

              
                $path = $file->store('appointments/' . $appointment->id . '/documentations', 's3');
     
                $documentation->url = $path;
                $documentation->save();

                return $documentation;
            }
        }

        return $path;
    }

    /**
     * Eliminar medicamentos a pacientes
     */
    public function destroy(Documentation $documentation)
    {
        //$result = Labresult::find($id);
        $fileToDelete = $documentation->url;

        $documentation->delete();

        if( Storage::disk('s3')->exists($fileToDelete) ){
            Storage::disk('s3')->delete($fileToDelete);
        }
       

        return '';
    }
}
