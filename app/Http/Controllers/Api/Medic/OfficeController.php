<?php

namespace App\Http\Controllers\Api\Medic;

use App\Enums\OfficeType;
use App\Repositories\ScheduleRepository;
use App\Schedule;
use Illuminate\Http\Request;
use App\Repositories\OfficeRepository;
use Validator;
use App\Office;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

class OfficeController extends Controller
{

    public function __construct(protected OfficeRepository $officeRepo, protected ScheduleRepository $scheduleRepo)
    {
        $this->middleware('auth');


    }

    public function index()
    {
        $search = request()->all();
        $search['active'] = 1;
        $search['dir'] = 'ASC';

        if (request('medic')) {
            $offices = $this->officeRepo->findAllByDoctor(auth()->user(), $search);
        } else {
            $offices = $this->officeRepo->findAll($search);
        }


        return $offices;
    }

    public function show(Office $office)
    {
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


                return response(['message' => 'Ya tienes registrado esta clínica'], 422);
            }
        }

        $office = auth()->user()->offices()->save($office);

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

    public function activateAppointmentRequestsToday(Office $office)
    {
        $medic = $office->administrator(['medico']);
        $maxTime = $medic?->getSetting('maxTime') ?? '18:00:00';

        $data = [
            'title' => $office->name.' (Solicitud)',
            'date' => today(),
            'start' => now()->setMinutes(0)->setSeconds(0)->toDateTimeLocalString(),
            'end' => now()->setTimeFromTimeString($maxTime)->toDateTimeLocalString(),
            'office_id' => $office->id,
            'user_id' => $medic?->id,
            'allDay' => 0,
            'backgroundColor' => request('backgroundColor') ?? '#67BC9A',
            'borderColor' => request('borderColor') ?? '#67BC9A'
        ];

        if (!request('activate')) {

            return Schedule::where('office_id', $office->id)
                ->whereDate('date', today())
                ->delete();

        }

        return $this->scheduleRepo->store($data);
    }

    public function store()
    {
        $data = request()->all();

        $v = Validator::make($data, [
            'type' => 'required',
            'name' => 'required',
            'address' => 'required',
            'province' => 'required',
            'canton' => 'required',
            'district' => 'required',
            'lat' => 'required',
            'lon' => 'required',
            'phone' => 'required',
            'whatsapp_number' => 'required',
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
            $file = request()->file('file');

            $ext = $file->guessClientExtension();

            if (in_array($ext, $mimes)) {
                //$fileUploaded = $file->storeAs('offices/' . $office->id, 'photo.jpg', 'public');
                $office->update([
                    'logo_path' => request()->file('file')->store('offices', 's3')
                ]);
            }
        }


        if ($office->type === OfficeType::MEDIC_OFFICE) {

            return $office;
        }

        //$medic = auth()->user();

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


        $v = Validator::make($data, [
            'type' => 'required',
            'name' => 'required',
            'address' => 'required',
            'province' => 'required',
            'canton' => 'required',
            'district' => 'required',
            'lat' => 'required',
            'lon' => 'required',
            'phone' => 'required',
            'whatsapp_number' => 'required',
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

        $data = Arr::except($data, array('logo_path', 'settings'));


        $office = $this->officeRepo->update($office->id, $data);

        $mimes = ['jpg', 'jpeg', 'bmp', 'png'];

        if (request()->file('file')) {
            $file = request()->file('file');

            $ext = $file->guessClientExtension();

            if (in_array($ext, $mimes)) {
                //$fileUploaded = $file->storeAs('offices/' . $office->id, 'photo.jpg', 'public');
                $office->update([
                    'logo_path' => request()->file('file')->store('offices', 's3')
                ]);
            }
        }

        return $office;
    }
}
