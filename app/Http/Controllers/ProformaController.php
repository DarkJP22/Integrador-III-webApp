<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Patient;
use Illuminate\Support\Facades\Validator;
use App\Office;
use App\User;
use App\Appointment;
use App\Mail\SendProforma;
use App\Proforma;
use Illuminate\Support\Arr;
use NumberFormatter;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ProformaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
       
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if(auth()->user()->hasRole('medico')){

            return redirect('/medic/proformas');
        }

        if (auth()->user()->hasRole('asistente')) {

            return redirect('/assistant/proformas');
        }

        if (auth()->user()->hasRole('clinica')) {

            return redirect('/clinic/proformas');
        }

        if (auth()->user()->hasRole('laboratorio')) {

            return redirect('/lab/proformas');
        }

        return redirect('/medic/proformas');

       
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'cliente' => 'required'

        ]);


        $data = $this->prepareData(request()->all());

        return \DB::transaction(function () use ($data) {

            $proforma = Proforma::create($data);

            return $proforma->saveLines($data['lines']);

        });

    }

    private function prepareData($data)
    {

        $data['consecutivo'] = $this->crearConsecutivo($data['TipoDocumento'], $data['office_id']);



         if($data['TipoDocumento'] != '01' && $data['TipoDocumento'] != '04'){
            $data = Arr::except($data, array('id'));
            $data = Arr::except($data, array('updated_at'));
            $data = Arr::except($data, array('created_at'));
            $data = Arr::except($data, array('created_by'));

        }

        $data['TotalWithNota'] = $data['TotalComprobante'];
        $data['created_by'] = isset($data['created_by']) ? $data['created_by'] : auth()->id();

        if(auth()->user()->isMedic() || auth()->user()->isClinic() || auth()->user()->isLab()){

            $data['user_id'] = (isset($data['user_id']) && $data['user_id']) ? $data['user_id'] : auth()->id();

        }else{
            $assistantUser = \DB::table('assistants_users')->where('assistant_id', auth()->id())->first();

            $data['user_id'] = (isset($data['user_id']) && $data['user_id']) ? $data['user_id'] : $assistantUser->user_id;

        }


        return $data;
    }

    public function crearConsecutivo($tipoDocumento, $officeId)
    {
        //$setting = \App\Setting::first();

        $consecutivo_inicio = 1;


        $consecutivo = Proforma::where('office_id', $officeId)
                                ->where('TipoDocumento', $tipoDocumento)
                                ->max('consecutivo');


        return ($consecutivo) ? $consecutivo += 1 : $consecutivo_inicio;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Proforma::class);

        $patient = false;
        $medic = false;
        $appointment = false;

        if(request('p')){
            $patient = Patient::find(request('p'));
            $patient->load('discounts');
        }

        if (request('m')) {

            $medic = User::where('id', request('m'))->whereHas('roles', function ($query) {
                $query->where('name', 'medico');
            })->first();


        }

        if (request('appointment')) {
            $appointment = Appointment::find(request('appointment'));
        }

        if (auth()->user()->hasRole('medico')) {
            return view('proformas.create', [
                'tipoDocumento' => '04',
                'patient' => $patient,
                'appointment' => $appointment,
            ]);
        }
        if (auth()->user()->hasRole('asistente')) {
            return view('assistant.proformas.create', [
                'tipoDocumento' => '04',
                'patient' => $patient,
                'medic' => $medic,
                'appointment' => $appointment,
            ]);
        }
        if (auth()->user()->hasRole('clinica')) {
            return view('clinic.proformas.create', [
                'tipoDocumento' => '04',
                'patient' => $patient,
                'medic' => $medic,
                'appointment' => $appointment,
            ]);
        }
        if (auth()->user()->hasRole('laboratorio')) {
            return view('lab.proformas.create', [
                'tipoDocumento' => '04',
                'patient' => $patient,
                'medic' => $medic,
                'appointment' => $appointment,
            ]);
        }

    }

    public function update(Proforma $proforma)
    {
        $this->validate(request(), [
            'cliente' => 'required'

        ]);

        $data = $this->prepareData(request()->all());

        $proforma->load('lines.taxes', 'lines.discounts');

        return \DB::transaction(function () use ($proforma, $data) {

            $proforma->fill($data);
            $proforma->status = 1;
            $proforma->save();
            $proforma->lines->each->delete();

            return $proforma->saveLines($data['lines']);

        });

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Proforma $proforma)
    {
        $this->authorize('view', $proforma);

        $proforma->load('lines.taxes', 'lines.discounts');

        if (request()->wantsJson()) {
            return response($proforma, 200);
        }

        if (auth()->user()->hasRole('asistente')) {

            return view('assistant.proformas.edit', [
                'proforma' => $proforma,
                'tipoDocumento' => $proforma->TipoDocumento

            ]);
        }

        if (auth()->user()->hasRole('clinica')) {

            return view('clinic.proformas.edit', [
                'proforma' => $proforma,
                'tipoDocumento' => $proforma->TipoDocumento

            ]);
        }

        if (auth()->user()->hasRole('laboratorio')) {

            return view('lab.proformas.edit', [
                'proforma' => $proforma,
                'medic' => $proforma->user,
                'tipoDocumento' => $proforma->TipoDocumento

            ]);
        }

        return view('proformas.edit', [
            'proforma' => $proforma,
            'tipoDocumento' => $proforma->TipoDocumento

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print(Proforma $proforma)
    {
        $proforma->load('lines.taxes', 'lines.discounts');

        $formatLetras = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $TotalEnLetras = $formatLetras->format($proforma->TotalComprobante);

        if (auth()->user()->hasRole('asistente')) {

            return view('assistant.proformas.print', compact('proforma', 'TotalEnLetras'));
        }

        if (auth()->user()->hasRole('clinica')) {

            return view('clinic.proformas.print', compact('proforma', 'TotalEnLetras'));
        }

        return view('proformas.print', compact('proforma', 'TotalEnLetras'));
    }

    public function pdf(Proforma $proforma)
    {

        $formatLetras = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $TotalEnLetras = $formatLetras->format($proforma->TotalComprobante);

        $data = [
            'proforma' => $proforma->load('lines.taxes', 'clinic', 'customer', 'user', 'lines.discounts'),
            'TotalEnLetras' => $TotalEnLetras

        ];

        $pdf = \PDF::loadView('proformas.pdf', $data);
        $name = $proforma->NumeroConsecutivo ? $proforma->NumeroConsecutivo : $proforma->consecutivo;
        return $pdf->download($name . '.pdf');


    }

    public function sendpdf(Proforma $proforma)
    {
        $this->validate(request(), [
            'to' => 'required|email_array'
        ]);

        $emails = array_map('trim', array_filter(explode(',', request('to'))));

        $formatLetras = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $TotalEnLetras = $formatLetras->format($proforma->TotalComprobante);

        $data = [
            'proforma' => $proforma->load('lines.taxes', 'clinic', 'customer', 'user', 'lines.discounts'),
            'TotalEnLetras' => $TotalEnLetras
        ];

        $pdf = \PDF::loadView('proformas.pdf', $data);



        try {
            \Mail::to($emails)->send(new SendProforma($pdf->output(), $proforma));

            if (request()->wantsJson()) {
                return response([], 200);
            }

        } catch (TransportExceptionInterface $e) {  //Swift_RfcComplianceException
            if (request()->wantsJson()) {
                return response(['error'], 500);
            }
        }

    }

   

    
    
}
