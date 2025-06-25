<?php

namespace App\Http\Controllers;

use App\Actions\CreateUserDiscount;
use Illuminate\Http\Request;
use App\Invoice;
use App\Patient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Mail\SendInvoice;
use App\Office;
use App\Notifications\NewInvoice;
use App\User;
use App\Appointment;
use App\InvoiceLineDiscount;
use App\Proforma;
use App\Services\FacturaService;
use Illuminate\Support\Arr;
use NumberFormatter;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class InvoiceController extends Controller
{
    protected $invoice;

    public function __construct(FacturaService $facturaService)
    {
        $this->middleware('auth');
        $this->facturaService = $facturaService;
    }


    public function index()
    {

        if(auth()->user()->hasRole('medico')){

            return redirect('/medic/invoices');
        }

        if (auth()->user()->hasRole('asistente')) {

            return redirect('/assistant/invoices');
        }

        if (auth()->user()->hasRole('clinica')) {

            return redirect('/clinic/invoices');
        }
        if (auth()->user()->hasRole('laboratorio')) {

            return redirect('/lab/invoices');
        }

        return redirect('/medic/invoices');

       
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CreateUserDiscount $createUserDiscount)
    {
        // $this->validate(request(), [
        //     'cliente' => 'required',
        //     'pay_with' => 'numeric'

        // ]);

        $v = Validator::make($request->all(), [
            'pay_with' => 'numeric',
            'CodigoActividad' => 'required'
        ]);

        $v->sometimes('tipo_identificacion_cliente', 'required', function ($input) {
            return $input->identificacion_cliente != '' || ($input->TipoDocumento == '01' || $input->TipoDocumento == '08');
        });

        $v->sometimes('identificacion_cliente', 'required|numeric', function ($input) {
            return $input->tipo_identificacion_cliente != '' || ($input->TipoDocumento == '01' || $input->TipoDocumento == '08');
        });

        $v->sometimes('identificacion_cliente', 'digits:9', function ($input) {
            return $input->tipo_identificacion_cliente == '01';
        });

        $v->sometimes('identificacion_cliente', 'digits:10', function ($input) {
            return $input->tipo_identificacion_cliente == '02' || $input->tipo_identificacion_cliente == '04';
        });

        $v->sometimes('identificacion_cliente', 'digits_between:11,12', function ($input) {
            return $input->tipo_identificacion_cliente == '03';
        });

        $v->sometimes('cliente', 'required', function ($input) {
            return ($input->tipo_identificacion_cliente != '' && $input->identificacion_cliente != '') || ($input->TipoDocumento == '01' || $input->TipoDocumento == '08');
        });

        $v->validate();

        $config = null;

        $office = Office::findOrFail(request('office_id'));

        //if (auth()->user()->fe || ($office->fe == 1) ) {

            $config = $office->configFactura->first(); //auth()->user()->getObligadoTributario($office);

            if (!existsCertFile($config)) {

                $errors = [
                    'certificate' => ['Parece que no tienes el certificado de hacienda ATV instalado. Para poder continuar verfica este configurado correctamente dentro del consultorio correspondiente']
                ];


                return response()->json(['errors' => $errors], 422, []);
            }

        //}

        $data = $this->prepareData(request()->all(), $config);

       \DB::transaction(function () use ($data) {

            $this->invoice = Invoice::create($data);

            $this->invoice = $this->invoice->saveLines($data['lines']);

            $this->invoice = $this->invoice->saveReferencias($data['referencias']);

            if(isset($data['initialPayment']) && $data['initialPayment']){
                $this->invoice->payments()->create([
                    "amount" => $data['initialPayment']
                ]);
            }

            if($this->invoice->status){
                $this->invoice->accumulatedTweak(); // se deduce y luego genera del acumulado del paciente
                $this->invoice->commissionTweak();
            }

        });

        if ($config && $this->invoice->status) {

            $result = $this->facturaService->sendDocument($this->invoice);

            if (!$result) {
                $this->invoice->update([
                    'sent_to_hacienda' => 1
                ]);


            }
        }


    //    $user = User::where('ide', $this->invoice->identificacion_cliente)->first();

    //    if($user){ // si es usuario de gps medica se crea su historial de descuentos

    //         $porc_discount = $this->invoice->lines->first()->PorcentajeDescuento;

    //         $createUserDiscount->create($user,[
    //                 'amount' => $this->invoice->TotalVenta,
    //                 'discount' => $porc_discount,
    //                 'total_discount' => $this->invoice->TotalDescuentos,
    //                 'total' => $this->invoice->TotalComprobante,
    //                 'CodigoMoneda' => $this->invoice->CodigoMoneda
    //         ]);

    //    }

       if(!$this->invoice->status){
            $office->clinicsAssistants->each->notify(new NewInvoice($this->invoice));
       }

       return $this->invoice;


    }

    private function prepareData($data, $obligadoTributario = null)
    {

        $data['consecutivo'] = $obligadoTributario ? $this->crearConsecutivoHacienda($obligadoTributario, $data['TipoDocumento']) : $this->crearConsecutivo($data['TipoDocumento'], $data['office_id']);

        if ($obligadoTributario) {
            $data['obligado_tributario_id'] = $obligadoTributario->id;
            $data['sucursal'] = $obligadoTributario->sucursal;
            $data['pos'] = $obligadoTributario->pos;
            $data['fe'] = 1;
        }else{
            $data['obligado_tributario_id'] = 0;
        }

         if($data['TipoDocumento'] != '01' && $data['TipoDocumento'] != '04'){
            $data = Arr::except($data, array('id'));
            $data = Arr::except($data, array('updated_at'));
            $data = Arr::except($data, array('created_at'));
            $data = Arr::except($data, array('NumeroConsecutivo'));
            $data = Arr::except($data, array('clave_fe'));
            $data = Arr::except($data, array('status_fe'));
            $data = Arr::except($data, array('resp_hacienda'));
            $data = Arr::except($data, array('sent_to_hacienda'));
            $data = Arr::except($data, array('created_xml'));
            $data = Arr::except($data, array('created_by'));

        }

        $data['TotalWithNota'] = $data['TotalComprobante'];
        $data['created_by'] = isset($data['created_by']) ? $data['created_by'] : auth()->id();
        $data['affiliation_id'] = isset($data['affiliation_id']) ? $data['affiliation_id'] : 0;
        $data['discount_id'] = isset($data['discount_id']) ? $data['discount_id'] : 0;

        if(auth()->user()->isMedic() || auth()->user()->isClinic() || auth()->user()->isLab()){

            $data['user_id'] = (isset($data['user_id']) && $data['user_id']) ? $data['user_id'] : auth()->id();

        }else{
            $assistantUser = \DB::table('assistants_users')->where('assistant_id', auth()->id())->first();

            $data['user_id'] = (isset($data['user_id']) && $data['user_id']) ? $data['user_id'] : $assistantUser->user_id;

        }

        if($data['CondicionVenta'] == '02'){ //credito
            $data['cxc_pending_amount'] = $data['TotalComprobante'];
        }



        return $data;
    }

    public function crearConsecutivoHacienda($obligadoTributario, $tipoDocumento)
    {


        $consecutivo_inicio = 1;


        if ($tipoDocumento == '01') {
            $consecutivo_inicio = $obligadoTributario->consecutivo_inicio;
        }

        if ($tipoDocumento == '02') {
            $consecutivo_inicio = $obligadoTributario->consecutivo_inicio_ND;
        }

        if ($tipoDocumento == '03') {
            $consecutivo_inicio = $obligadoTributario->consecutivo_inicio_NC;
        }

        if ($tipoDocumento == '04') {
            $consecutivo_inicio = $obligadoTributario->consecutivo_inicio_tiquete;
        }

        $consecutivo = Invoice::where('obligado_tributario_id', $obligadoTributario->id)
                                ->where('TipoDocumento', $tipoDocumento)
                                ->where('sucursal', $obligadoTributario->sucursal ? $obligadoTributario->sucursal : 1)
                                ->where('pos', $obligadoTributario->pos ? $obligadoTributario->pos : 1)
                                // ->where('sent_to_hacienda', 1)
                                ->where('status', 1)
                                ->max('consecutivo');


        return ($consecutivo) ? $consecutivo += 1 : $consecutivo_inicio;
    }

    public function crearConsecutivo($tipoDocumento, $officeId)
    {
        //$setting = \App\Setting::first();

        $consecutivo_inicio = 1;


        $consecutivo = Invoice::where('user_id', auth()->id())
                                ->where('office_id', $officeId)
                                ->where('TipoDocumento', $tipoDocumento)
                                ->where('status', 1)
                                ->max('consecutivo');


        return ($consecutivo) ? $consecutivo += 1 : $consecutivo_inicio;
    }

    public function create()
    {
        $this->authorize('create', Invoice::class);

        $patient = false;
        $medic = false;
        $appointment = false;

        $proforma = null;
        if(request('pr')){
            $proforma = Proforma::with('lines.taxes', 'lines.discounts')->find(request('pr'));
        }

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
            return view('invoices.create', [
                'tipoDocumento' => '04',
                'patient' => $patient,
                'appointment' => $appointment,
                'proforma' => $proforma,

            ]);
        }
        if (auth()->user()->hasRole('asistente')) {
            return view('assistant.invoices.create', [
                'tipoDocumento' => '04',
                'patient' => $patient,
                'medic' => $medic,
                'appointment' => $appointment,
                'proforma' => $proforma,

            ]);
        }
        if (auth()->user()->hasRole('clinica')) {
            return view('clinic.invoices.create', [
                'tipoDocumento' => '04',
                'patient' => $patient,
                'medic' => $medic,
                'appointment' => $appointment,
                'proforma' => $proforma,

            ]);
        }
        if (auth()->user()->hasRole('laboratorio')) {
            return view('lab.invoices.create', [
                'tipoDocumento' => '04',
                'patient' => $patient,
                'medic' => ($proforma ? $proforma->user : null) ?? $medic,
                'appointment' => $appointment,
                'proforma' => $proforma,

            ]);
        }

    }

    public function update(Invoice $invoice)
    {
        $v = Validator::make(request()->all(), [
            'pay_with' => 'numeric'
        ]);

        $v->sometimes('tipo_identificacion_cliente', 'required', function ($input) {
            return $input->identificacion_cliente != '' || ($input->TipoDocumento == '01' || $input->TipoDocumento == '08');
        });

        $v->sometimes('identificacion_cliente', 'required|numeric', function ($input) {
            return $input->tipo_identificacion_cliente != '' || ($input->TipoDocumento == '01' || $input->TipoDocumento == '08');
        });

        $v->sometimes('identificacion_cliente', 'digits:9', function ($input) {
            return $input->tipo_identificacion_cliente == '01';
        });

        $v->sometimes('identificacion_cliente', 'digits:10', function ($input) {
            return $input->tipo_identificacion_cliente == '02' || $input->tipo_identificacion_cliente == '04';
        });

        $v->sometimes('identificacion_cliente', 'digits_between:11,12', function ($input) {
            return $input->tipo_identificacion_cliente == '03';
        });

        $v->sometimes('cliente', 'required', function ($input) {
            return ($input->tipo_identificacion_cliente != '' && $input->identificacion_cliente != '') || ($input->TipoDocumento == '01' || $input->TipoDocumento == '08');
        });

        $v->validate();

        $config = null;

        $office = Office::findOrFail(request('office_id'));

        if (auth()->user()->fe || ($office->fe == 1)) {

            $config = $invoice->obligadoTributario;

            if (!existsCertFile($config)) {

                $errors = [
                    'certificate' => ['Parece que no tienes el certificado de hacienda ATV instalado. Para poder continuar verfica que el médico lo tenga configurado en su perfil']
                ];


                return response()->json(['errors' => $errors], 422, []);
            }

        }
        $data = $this->prepareData(request()->all(), $config);

        \DB::transaction(function () use ($data, $invoice) {

            $invoice->fill($data);
            $invoice->status = 1;
            $invoice->save();
            $invoice->lines()->delete();
            $invoice->referencias()->delete();

            $invoice = $invoice->saveLines($data['lines']);

            $invoice = $invoice->saveReferencias($data['referencias']);

            if($invoice->CondicionVenta == 2) // credito
            {
                $invoice->calculatePendingAmount();
            }

            if($invoice->status){
                $invoice->accumulatedTweak();
                $invoice->commissionTweak();
            }

        });

        if ($config && $invoice->status) {

            $result = $this->facturaService->sendDocument($invoice);

            if (!$result) {
                $invoice->update([
                    'sent_to_hacienda' => 1
                ]);


            }
        }

        return $invoice;

    }

    public function sendHacienda(Invoice $invoice)
    {
        if ($this->comprobarRecepcion($invoice)) { //verificamos si ya fue enviado la factura y actualizamos status fe
            \Log::info('Documento Electronico ya habia sido enviado');
            return $invoice;
        }

        $result = $this->facturaService->sendDocument($invoice);

        if (!$result) {
            $invoice->update([
                'sent_to_hacienda' => 1,
                'status' => 1
            ]);

            return $invoice;
        }

        return response(['message' => 'Error al enviar'], 422);
    }

    public function comprobarRecepcion(Invoice $invoice)
    {
        $config = $invoice->obligadoTributario;
        $clave = $invoice->clave_fe;
        $respHacienda = $this->facturaService->recepcionDocument($clave);


        if (isset($respHacienda->{'ind-estado'})) {

            if ($invoice->status_fe != 'aceptado') {
                if ($invoice->status_fe != 'rechazado' && $respHacienda->{'ind-estado'} == 'rechazado') {
                    $invoice->rollbackAccumulatedTweak();
                }
                $invoice->status_fe = $respHacienda->{'ind-estado'};
            }

            if (isset($respHacienda->{'respuesta-xml'})) {
                $invoice->resp_hacienda = json_encode($this->facturaService->decodeRespuestaXML($respHacienda->{'respuesta-xml'}));
            }

            $invoice->sent_to_hacienda = 1;
            $invoice->save();

            return true;
        }

        return false;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load('lines.taxes', 'referencias', 'lines.discounts', 'affiliation');
        if (request()->wantsJson()) {
            return response($invoice, 200);
        }

        if (auth()->user()->hasRole('asistente')) {

            return view('assistant.invoices.edit', [
                'invoice' => $invoice,
                'tipoDocumento' => $invoice->TipoDocumento

            ]);
        }

        if (auth()->user()->hasRole('clinica')) {

            return view('clinic.invoices.edit', [
                'invoice' => $invoice,
                'tipoDocumento' => $invoice->TipoDocumento

            ]);
        }
        if (auth()->user()->hasRole('laboratorio')) {

            return view('lab.invoices.edit', [
                'invoice' => $invoice,
                'medic' => $invoice->user,
                'tipoDocumento' => $invoice->TipoDocumento

            ]);
        }


        return view('invoices.edit', [
            'invoice' => $invoice,
            'tipoDocumento' => $invoice->TipoDocumento

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print(Invoice $invoice)
    {
        $invoice->load('lines.discounts');

        foreach($invoice->lines as $line){
            if($line->PorcentajeDescuento > 0 &&
                $line->MontoDescuento > 0 &&
                $line->discounts->count() == 0){

                    InvoiceLineDiscount::create([
                        "invoice_line_id" => $line->id,
                        "PorcentajeDescuento" => $line->PorcentajeDescuento,
                        "MontoDescuento" => $line->MontoDescuento,
                        "NaturalezaDescuento" => $line->NaturalezaDescuento ?? "Descuento Cliente",
                    ]);

            }

        }

        $invoice = $invoice->load('lines.taxes', 'referencias', 'lines.discounts');

        $formatLetras = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $TotalEnLetras = $formatLetras->format($invoice->TotalComprobante);

        if (auth()->user()->hasRole('asistente')) {

            return view('assistant.invoices.print', compact('invoice', 'TotalEnLetras'));
        }

        if (auth()->user()->hasRole('clinica')) {

            return view('clinic.invoices.print', compact('invoice', 'TotalEnLetras'));
        }
        if (auth()->user()->hasRole('laboratorio')) {

            return view('lab.invoices.print', compact('invoice', 'TotalEnLetras'));
        }

        return view('invoices.print', compact('invoice', 'TotalEnLetras'));
    }

    public function xml(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $config = $invoice->obligadoTributario;


        if (!Storage::disk('local')->exists('facturaelectronica/' . $config->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml')) {
            flash('Archivo no encontrado', 'danger');

            return back();
        }

        return Storage::disk('local')->download('facturaelectronica/' . $config->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml');

       // return response()->download($pathToFile);
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('lines.discounts');

        foreach($invoice->lines as $line){
            if($line->PorcentajeDescuento > 0 &&
                $line->MontoDescuento > 0 &&
                $line->discounts->count() == 0){

                    InvoiceLineDiscount::create([
                        "invoice_line_id" => $line->id,
                        "PorcentajeDescuento" => $line->PorcentajeDescuento,
                        "MontoDescuento" => $line->MontoDescuento,
                        "NaturalezaDescuento" => $line->NaturalezaDescuento ?? "Descuento Cliente",
                    ]);

            }

        }

        $formatLetras = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $TotalEnLetras = $formatLetras->format($invoice->TotalComprobante);

        $data = [
            'invoice' => $invoice->load('lines.taxes', 'clinic', 'customer', 'user', 'lines.discounts'),
            'TotalEnLetras' => $TotalEnLetras

        ];

        $pdf = \PDF::loadView('invoices.pdf', $data);
        $name = $invoice->NumeroConsecutivo ? $invoice->NumeroConsecutivo : $invoice->consecutivo;
        return $pdf->download($name . '.pdf');


    }

    public function sendpdf(Invoice $invoice)
    {
        $this->validate(request(), [
            'to' => 'required|email_array'
        ]);

        $invoice->load('lines.discounts');

        foreach($invoice->lines as $line){
            if($line->PorcentajeDescuento > 0 &&
                $line->MontoDescuento > 0 &&
                $line->discounts->count() == 0){

                    InvoiceLineDiscount::create([
                        "invoice_line_id" => $line->id,
                        "PorcentajeDescuento" => $line->PorcentajeDescuento,
                        "MontoDescuento" => $line->MontoDescuento,
                        "NaturalezaDescuento" => $line->NaturalezaDescuento ?? "Descuento Cliente",
                    ]);

            }

        }

        $emails = array_map('trim', array_filter(explode(',', request('to'))));

        $formatLetras = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $TotalEnLetras = $formatLetras->format($invoice->TotalComprobante);

        $data = [
            'invoice' => $invoice->load('lines.taxes', 'clinic', 'customer', 'user', 'lines.discounts'),
            'TotalEnLetras' => $TotalEnLetras
        ];

        $pdf = \PDF::loadView('invoices.pdf', $data);

        $config = $invoice->obligadoTributario;

        $respHacienda = $this->facturaService->recepcionDocument($invoice->clave_fe);

        $this->saveRespuestaXML($config, $invoice->clave_fe, $respHacienda);

        if (!Storage::disk('local')->exists('facturaelectronica/' . $config->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml')) {
            //flash('Archivo XML no encontrado', 'danger');

            if (request()->wantsJson()) {
                return response('Archivo XML no encontrado', 404);
            }
        }

        $xmlFactura = Storage::disk('local')->get('facturaelectronica/' . $config->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml');

        $xmlRespuestaHaciendaFactura = null;

        if (Storage::disk('local')->exists('facturaelectronica/' . $config->id . '/gpsm_mensaje_hacienda_' . $invoice->clave_fe . '.xml')) {

            $xmlRespuestaHaciendaFactura = Storage::disk('local')->get('facturaelectronica/' . $config->id . '/gpsm_mensaje_hacienda_' . $invoice->clave_fe . '.xml');
        }


        try {
            \Mail::to($emails)->send(new SendInvoice($pdf->output(), $xmlFactura, $xmlRespuestaHaciendaFactura, $invoice));

            if (request()->wantsJson()) {
                return response([], 200);
            }

        } catch (TransportExceptionInterface $e) {  //Swift_RfcComplianceException
            \Log::error($e->getMessage());
            if (request()->wantsJson()) {
                return response(['error'], 500);
            }
        }

    }

    public function saveRespuestaXML($config, $clave, $respHacienda)
    {
        if (isset($respHacienda->{'respuesta-xml'})) {
            Storage::disk('local')->put('facturaelectronica/' . $config->id . '/pos_mensaje_hacienda_' . $clave . '.xml', base64_decode($respHacienda->{'respuesta-xml'}));
        }
    }

    /**
     * imprime resumen de la consulta
     */
    public function makePdf(Invoice $invoice)
    {

        $html = request('htmltopdf');
        $pdf = new PDF($orientation = 'L', $unit = 'in', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false);

        $pdf::SetFont('helvetica', '', 9);

        $pdf::SetTitle('Expediente Clínico');
        $pdf::AddPage('L', 'A4');
        $pdf::writeHTML($html, true, false, true, false, '');

        $pdf::Output('gpsm_' . $invoice->clave_fe . '.pdf');
    }

    
}
