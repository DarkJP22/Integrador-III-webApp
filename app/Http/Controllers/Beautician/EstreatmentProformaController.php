<?php

namespace App\Http\Controllers\Beautician;

use App\Appointment;
use App\Estreatment;
use App\Http\Controllers\Controller;
use App\Optreatment;
use App\Product;
use App\Proforma;
use Illuminate\Http\Request;

class EstreatmentProformaController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Appointment $appointment)
    {
        $data = $this->validate(request(), [
            'items.*.id' => ['required'],
            'items.*.name' => ['required'],
            'items.*.discount' => ['required', 'numeric'],
            'items.*.sessions' => ['required', 'numeric'],
            'items.*.price' => ['required', 'numeric'],
            'items.*.subtotal' => ['required', 'numeric']
        ]);

        $data = $this->prepareData($data, $appointment);

        return \DB::transaction(function () use ($data) {

            $proforma = Proforma::create($data);

            return $proforma->saveLines($data['lines']);
        });
    }

    private function prepareData($data, $appointment)
    {

        $data['TipoDocumento'] = '04';
        $data['office_id'] = $appointment->office_id;
        $data['appointment_id'] = $appointment->id;
        $data['customer_id'] = $appointment->patient_id;
        $data['created_by'] = auth()->id();
        $data['user_id'] = auth()->id();

        $patient =  $appointment->patient;
        $data['tipo_identificacion_cliente'] = $patient->tipo_identificacion;
        $data['identificacion_cliente'] = $patient->ide;
        $data['cliente'] = $patient->first_name;
        $data['email'] = $patient->email;
        $data['discount_id'] = 0;
        $data['consecutivo'] = $this->crearConsecutivo($data['TipoDocumento'], $data['office_id']);


        foreach ($data['items'] as $item) {
            $opTreatment =  Optreatment::find($item['id']);
            $product = Product::find($opTreatment->product_id);

            if (!$product) {

                $product = new Product([
                    'CodigoProductoHacienda' => null,
                    'code' => 'NO-DEFINIDO',
                    'name' => $item['name'],
                    'measure' => 'Sp',
                    'price' => $item['price']

                ]);
            }

            $montoTotal = $item['sessions'] * $product->price;
            $montoDescuento = $montoTotal * ($item['discount'] / 100);
            $subTotal = $montoTotal - $montoDescuento;

            $line = [
                "CodigoProductoHacienda" => $product->CodigoProductoHacienda,
                "Codigo" => $product->code ?? 'NO-DEFINIDO',
                "Detalle" => $product->name,
                "Cantidad" => $item['sessions'],
                "UnidadMedida" => $product->measure,
                "PrecioUnitario" => $product->price,
                "MontoTotal" => $montoTotal,
                "PorcentajeDescuento" => 0,
                "MontoDescuento" => 0,
                "NaturalezaDescuento" => null,
                "discounts" => [],
                "SubTotal" => $subTotal,
                "MontoTotalLinea" =>  $item['subtotal'],
                "totalTaxes" => 0,
                "taxes" => [],
                "type" => "S",
                "laboratory" => 0,
                "is_servicio_medico" => 1,
                "BaseImponible" => $subTotal
            ];

            if ($item['discount']) {
                $line['discounts'][] = [
                    "PorcentajeDescuento" => $item['discount'],
                    "MontoDescuento" => $montoDescuento,
                    "NaturalezaDescuento" => "Descuento paciente",
                ];
            }


            foreach ($product->taxes as $tax) {
                $line['taxes'][] =     [
                    "code" => $tax->code,
                    "CodigoTarifa" => $tax->CodigoTarifa,
                    "name" => $tax->name,
                    "tarifa" => $tax->tarifa,
                    "amount" => 0,
                    "PorcentajeExoneracion" => 0,
                    "MontoExoneracion" => 0,
                    "TarifaOriginal" => $tax->tarifa,
                    "ImpuestoOriginal" => 0,
                    "ImpuestoNeto" => 0,
                ];
            }


            $data['lines'][] = $line;
        }

        return $data;
    }

    public function crearConsecutivo($tipoDocumento, $officeId)
    {

        $consecutivo_inicio = 1;


        $consecutivo = Proforma::where('office_id', $officeId)
            ->where('TipoDocumento', $tipoDocumento)
            ->max('consecutivo');


        return ($consecutivo) ? $consecutivo += 1 : $consecutivo_inicio;
    }
}
