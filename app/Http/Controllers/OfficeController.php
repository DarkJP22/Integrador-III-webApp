<?php

namespace App\Http\Controllers;

use App\Enums\OfficeType;
use Illuminate\Http\Request;
use App\Repositories\OfficeRepository;
use Validator;
use App\Office;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewMedicRequest;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Arr;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class OfficeController extends Controller
{
    
    public function __construct(protected OfficeRepository $officeRepo) {
        $this->middleware('auth');
    }

    public function index()
    {
        $search = request()->all();
        $search['active'] = 1;
        $search['dir'] = 'ASC';

        if(request('medic')){
            $offices = $this->officeRepo->findAllByDoctor(auth()->user(), $search);
        }else{
            $offices = $this->officeRepo->findAll($search);
        }

        return $offices;
    }

    public function store()
    {
        $data = request()->all();
        $data['utiliza_agenda_gps'] = request()->boolean('utiliza_agenda_gps');
        
        $v = Validator::make($data, [
            'type' => 'required',
            'name' => 'required',
            'address' => 'required',
            'province' => 'required',
            'canton' => 'required',
            'district' => 'required',
            'phone' => 'required',
            'utiliza_agenda_gps' => 'sometimes',
            'file' => 'mimes:jpg,jpeg,bmp,png',
        ]);

        $v->sometimes('ide', 'required', function ($input) {
            return $input->bill_to == 'C';
        });
        $v->sometimes('ide_name', 'required', function ($input) {
            return $input->bill_to == 'C';
        });

        $v->validate();

        $data['verified'] = 0;
        $data['obligado_tributario'] = 'C';
        $data['created_by'] = auth()->id();
        $data['original_type'] = $data['type'];
        
        if ($data['type'] == 1) {
            $data['active'] = 1;
            $data['verified'] = 1;
            $data['obligado_tributario'] = 'M';
        }

        $office = $this->officeRepo->store($data);

        $mimes = ['jpg', 'jpeg', 'bmp', 'png'];
       
        
        if (request()->file('file')) {
            try{
                $file = request()->file('file');

                $ext = $file->guessClientExtension();

                if (in_array($ext, $mimes)) {
                    //$fileUploaded = $file->storeAs('offices/' . $office->id, 'photo.jpg', 'public');
                    $office->update([
                        'logo_path' => request()->file('file')->store('offices', 's3')
                    ]);
                }
            }catch(S3Exception $e){
                \Log::error($e->getMessage());
            }
        }

      

        if ($office->type === OfficeType::MEDIC_OFFICE) {

            return $office;
        }

        $medic = auth()->user();

        // try {
        //     \Mail::to($this->administrators)->send(new NewOffice($office, $medic));
        // } catch (TransportExceptionInterface $e) {  //Swift_RfcComplianceException
        //     \Log::error($e->getMessage());
        // }

        return $office;
    }

    public function update(Office $office)
    {
        $data = request()->all();
        $data['utiliza_agenda_gps'] = request()->boolean('utiliza_agenda_gps');
 
        $v = Validator::make($data, [
            'type' => 'required',
            'name' => 'required',
            'address' => 'required',
            'province' => 'required',
            'canton' => 'required',
            'district' => 'required',
            'phone' => 'required',
            'utiliza_agenda_gps' => 'sometimes',
            'file' => 'mimes:jpg,jpeg,bmp,png',
            'lat' => 'nullable',
            'lon' => 'nullable',
        ]);

        $v->sometimes('ide', 'required', function ($input) {
            return $input->bill_to == 'C';
        });
        $v->sometimes('ide_name', 'required', function ($input) {
            return $input->bill_to == 'C';
        });

        $v->validate();

        $data = Arr::except($data, array('logo_path', 'settings'));


        $office = $this->officeRepo->update($office->id, $data);

        $mimes = ['jpg', 'jpeg', 'bmp', 'png'];
      
        if (request()->file('file')) {
            try{
                $file = request()->file('file');

                $ext = $file->guessClientExtension();

                if (in_array($ext, $mimes)) {
                    //$fileUploaded = $file->storeAs('offices/' . $office->id, 'photo.jpg', 'public');
                    $office->update([
                        'logo_path' => request()->file('file')->store('offices', 's3')
                    ]);
                }
            }catch(S3Exception $e){
                \Log::error($e->getMessage());
            }
        }

        return $office;
        
    }

    /**
     * Actualizar datos de consultorio
     */
    public function destroy($id)
    {

        $office = Office::findOrFail($id);

        $res = auth()->user()->offices()->detach($office->id);

        if ($office->type === OfficeType::MEDIC_OFFICE) {
            $this->officeRepo->delete($id);
        }

        //Session::forget('office_id');

        if (request()->wantsJson()) {

            
            return response([], 204);
           

        }

        return back();
    }

    public function assign(Office $office)
    {
      

        if (auth()->user()->hasOffice($office->id)) {

            if (request()->wantsJson()) {


                return response(['message'=>'Ya tienes registrado esta clínica'], 422);


            }
        }

        $office = auth()->user()->offices()->save($office, ['obligado_tributario' => request('obligado_tributario')]);

        try {

            Notification::send($office->administrators(), new NewMedicRequest(auth()->user()));


        } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
        {
            \Log::error($e->getMessage());
        }    

        // if (!session()->has('office_id') || session('office_id') == '') {
        //     session(['office_id' => $office->id]);
        // }


        // event(new MedicRequest(auth()->user(), $office->administrators()));

        // try {
        //     \Mail::to($office->administrators())->send(new NewOffice($office, $medic));
        // } catch (TransportExceptionInterface $e) {  //Swift_RfcComplianceException
        //     \Log::error($e->getMessage());
        // }
        

        return $office;
    }


   
    
}
