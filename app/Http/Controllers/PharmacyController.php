<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\PharmacyRepository;
use Validator;
use App\Office;
use App\Pharmacy;
use Illuminate\Support\Arr;

class PharmacyController extends Controller
{
    
    public function __construct(PharmacyRepository $pharmacyRepo) {
        $this->middleware('auth');
        
        $this->pharmacyRepo = $pharmacyRepo;
    }


    public function update(Pharmacy $pharmacy)
    {
        $data = request()->all();
      

        $v = Validator::make($data, [
            'name' => 'required',
            'address' => 'required',
            'province' => 'required',
            'canton' => 'required',
            'district' => 'required',
            'phone' => 'required',
            'file' => 'mimes:jpg,jpeg,bmp,png',
        ]);


        $v->validate();

        $data = Arr::except($data, array('logo_path'));


        $office = $this->pharmacyRepo->update($pharmacy->id, $data);

        $mimes = ['jpg', 'jpeg', 'bmp', 'png'];
      
        if (request()->file('file')) {
            $file = request()->file('file');

            $ext = $file->guessClientExtension();

            if (in_array($ext, $mimes)) {
                
                $pharmacy->update([
                    'logo_path' => request()->file('file')->store('pharmacies', 's3')
                ]);
            }
        }

        return $pharmacy;
        
    }

   
    
}
