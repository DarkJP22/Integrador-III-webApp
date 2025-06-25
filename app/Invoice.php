<?php

namespace App;

use App\Actions\CreateAccumulatedTransaction;
use App\Actions\CreateCommission;
use App\Actions\CreateCommissionTransaction;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Invoice extends Model
{
    protected $guarded = ['lines', 'referencias', 'TipoDocumentoName', 'CondicionVentaName', 'MedioPagoName', 'Pending', 'initialPayment', 'payments', 'user', 'phone'];

    protected $casts = [
        'acumulado_utilizado' => 'float',
        'utiliza_acumulado_afiliado' => 'integer',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected $appends = ['TipoDocumentoName', 'CondicionVentaName', 'MedioPagoName'];


    public function getTipoDocumentoNameAttribute()
    {
        return trans('utils.tipo_documento.' . $this->TipoDocumento);
    }
    public function getCondicionVentaNameAttribute()
    {
        return trans('utils.condicion_venta.' . $this->CondicionVenta);
    }
    public function getMedioPagoNameAttribute()
    {
        return trans('utils.medio_pago.' . $this->MedioPago);
    }

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->where('cliente', 'like', '%' . $search['q'] . '%')
                    ->orWhere('identificacion_cliente', 'like', '%' . $search['q'] . '%')
                    ->orWhere('consecutivo', 'like', '%' . $search['q'] . '%')
                    ->orWhere('NumeroConsecutivo', 'like', '%' . $search['q'] . '%');
            });
        }

        if (isset($search['start']) && $search['start']) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $query = $query->where([
                ['created_at', '>=', $start],
                ['created_at', '<=', $end->endOfDay()]
            ]);
        }
        if (isset($search['office']) && $search['office']) {

            $query = $query->where('office_id', $search['office']);
        }
        if (isset($search['medic']) && $search['medic']) {

            $query = $query->where('user_id', $search['medic']);
        }

        if (isset($search['type']) && $search['type']) {

            $query = $query->where('TipoDocumento', $search['type']);
        }
        if (isset($search['condicion']) && $search['condicion']) {

            $query = $query->where('CondicionVenta', $search['condicion']);
        }

        if (isset($search['CodigoActividad']) && $search['CodigoActividad']) {

            $query = $query->where('CodigoActividad', $search['CodigoActividad']);
        }


        return $query;
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }
    public function referencias()
    {
        return $this->hasMany(Referencia::class);
    }
    public function payments() //abonos
    {
        return $this->hasMany(Payment::class);
    }

    public function notascreditodebito()
    {
        return $this->hasMany(Referencia::class, 'referencia_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customer()
    {
        return $this->belongsTo(Patient::class, 'customer_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function obligadoTributario()
    {
        return $this->belongsTo(ConfigFactura::class, 'obligado_tributario_id');
    }


    public function transaction()
    {
        return $this->morphOne(AffiliationTransaction::class, 'transactable');
    }

    public function affiliation()
    {
        return $this->belongsTo(Accumulated::class, 'affiliation_id');
    }

    public function accumulatedTransaction()
    {
        return $this->morphOne(AccumulatedTransaction::class, 'resource');
    }

    public function commissionTransaction()
    {
        return $this->morphOne(CommissionTransaction::class, 'resource');
    }

    public function saveLines($items)
    {


        foreach ($items as $item) {

            $item = Arr::except($item, array('id', 'invoice_id'));

            $line = $this->lines()->create($item);
            $line->saveTaxes($item['taxes']);
            $line->saveDiscounts($item['discounts']);

            if($this->user?->commission_affiliation){

                if($line->reference_commission){
                    $line->total_commission = $line->SubTotal * (((Configuration::first()?->porc_reference_commission) ?? 0) / 100);
                }else{
                    $line->total_commission = $line->SubTotal * (((Configuration::first()?->porc_commission) ?? 0) / 100);
                }
    
                if($line->no_aplica_commission){
                    $line->total_commission = 0;
                }
    
                $line->save();

            }
            

        }

        return $this;
    }

    public function saveReferencias($referencias)
    {
        foreach ($referencias as $ref) {

            $referencia = $this->referencias()->create($ref);
        }

        return $this;
    }

    public function calculatePendingAmount()
    {
        $paymentsAmount = 0;

        $paymentsAmount = $this->payments->sum('amount');

        if ($this->notascreditodebito->count()) {
            $pending = $this->TotalWithNota - $paymentsAmount;
        } else {
            $pending = $this->TotalComprobante - $paymentsAmount;
        }

        $this->update([
            'cxc_pending_amount' => ($pending < 0) ? 0 : $pending
        ]);
    }

    public function getTotalCommission()
    {
        return $this->lines->sum('total_commission');
    }

    public function rollbackAccumulatedTweak()
    {
        $this->accumulatedTweak(true);
    }

    public function accumulatedTweak($rollback = false)
    {

        if ($this->TipoDocumento == '01' || $this->TipoDocumento == '04') { //factura y tiquete
            $this->accumulatedTweakFacturaTiquete($rollback);
            $this->generateAccumulatedTweakFacturaTiquete($rollback);
        }

        if ($this->TipoDocumento == '02') {  //nota debito
            $this->accumulatedTweakNotaDebito($rollback);
            $this->generateAccumulatedTweakNotaDebito($rollback);
        }

        if ($this->TipoDocumento == '03') {  //nota credito
            $this->accumulatedTweakNotaCredito($rollback);
            $this->generateAccumulatedTweakNotaCredito($rollback);
        }
    }
    public function accumulatedTweakFacturaTiquete($rollback)
    {
        $createAccumulatedTransaction = resolve(CreateAccumulatedTransaction::class);
        $multiple = $rollback ? 1 : -1;
        $action = $rollback ? 'Rollback Factura' : 'Factura';
        $acumuladoUtilizado = $this->acumulado_utilizado;
        $accumulated = Accumulated::find($this->affiliation_id);

        if ($accumulated && $this->utiliza_acumulado_afiliado && ((floatval($accumulated->acumulado) > 0 && !$rollback) || $rollback)) {
            $createAccumulatedTransaction($accumulated, $action, $acumuladoUtilizado * $multiple, $this, $this->clinic?->name);
        }
    }

    public function accumulatedTweakNotaDebito($rollback)
    {
        $createAccumulatedTransaction = resolve(CreateAccumulatedTransaction::class);

        $multiple = $rollback ? 1 : -1;
        $action = $rollback ? 'Rollback Nota Debito' : 'Nota Debito';
        $referencia = $this->referencias()->latest()->first();
        $originalInvoice = $referencia ? $referencia->originalInvoice : null;
        $accumulated = Accumulated::find($this->affiliation_id);

        if ($referencia && $originalInvoice) {

            $originalAcumuladoUtilizado = $originalInvoice->acumulado_utilizado;

            $currentAcumuladoUtilizado = $this->acumulado_utilizado;

            if ($originalAcumuladoUtilizado != $currentAcumuladoUtilizado) {
                $acumuladoUtilizado = $currentAcumuladoUtilizado - $originalAcumuladoUtilizado;
            } else {
                $acumuladoUtilizado = $currentAcumuladoUtilizado;
            }
        } else { // no exite referencia en base de datos para comparar

            $acumuladoUtilizado = $this->acumulado_utilizado;
        }

        if ($accumulated && $this->utiliza_acumulado_afiliado) {
            $createAccumulatedTransaction($accumulated, $action, $acumuladoUtilizado * $multiple, $this, $this->clinic?->name);
        }
    }

    public function accumulatedTweakNotaCredito($rollback)
    {
        $createAccumulatedTransaction = resolve(CreateAccumulatedTransaction::class);
        $multiple = $rollback ? -1 : 1;
        $action = $rollback ? 'Rollback Nota Credito' : 'Nota Credito';
        $referencia = $this->referencias()->latest()->first();
        $originalInvoice = $referencia ? $referencia->originalInvoice : null;
        $accumulated = Accumulated::find($this->affiliation_id);

        if ($referencia && $originalInvoice) {

            $originalAcumuladoUtilizado = $originalInvoice->acumulado_utilizado;

            $currentAcumuladoUtilizado = $this->acumulado_utilizado;

            if ($originalAcumuladoUtilizado != $currentAcumuladoUtilizado) {
                $acumuladoUtilizado = $originalAcumuladoUtilizado - $currentAcumuladoUtilizado;
            } else {
                $acumuladoUtilizado = $currentAcumuladoUtilizado;
            }
        } else { // no exite referencia en base de datos para comparar

            $acumuladoUtilizado = $this->acumulado_utilizado;
        }

        if ($accumulated && $this->utiliza_acumulado_afiliado) {
            $createAccumulatedTransaction($accumulated, $action, $acumuladoUtilizado * $multiple, $this, $this->clinic?->name);
        }
    }

    public function generateAccumulatedTweakFacturaTiquete($rollback)
    {
        $createAccumulatedTransaction = resolve(CreateAccumulatedTransaction::class);
        $multiple = $rollback ? -1 : 1;
        $action = $rollback ? 'Rollback Factura Generacion' : 'Factura Generacion';
        $acumuladoGenerado =  floatval($this->TotalComprobante) * (((Configuration::first()?->porc_accumulated) ?? 0) / 100);
        $accumulated = Accumulated::find($this->affiliation_id);

        if ($accumulated && ((floatval($accumulated->acumulado) > 0 && $rollback) || !$rollback)) {
            $createAccumulatedTransaction($accumulated, $action, $acumuladoGenerado * $multiple, $this, $this->clinic?->name);
        }
    }

    public function generateAccumulatedTweakNotaDebito($rollback)
    {
        $createAccumulatedTransaction = resolve(CreateAccumulatedTransaction::class);

        $multiple = $rollback ? -1 : 1;
        $action = $rollback ? 'Rollback Nota Debito Generacion' : 'Nota Debito Generacion';
        $referencia = $this->referencias()->latest()->first();
        $originalInvoice = $referencia ? $referencia->originalInvoice : null;
        $accumulated = Accumulated::find($this->affiliation_id);

        if ($referencia && $originalInvoice) {

            $originalAcumuladoGenerado = floatval($originalInvoice->TotalComprobante) * (((Configuration::first()?->porc_accumulated) ?? 0) / 100);

            $currentAcumuladoGenerado = floatval($this->TotalComprobante) * (((Configuration::first()?->porc_accumulated) ?? 0) / 100);

            if ($originalAcumuladoGenerado != $currentAcumuladoGenerado) {
                $acumuladoGenerado = $currentAcumuladoGenerado - $originalAcumuladoGenerado;
            } else {
                $acumuladoGenerado = $currentAcumuladoGenerado;
            }
        } else { // no exite referencia en base de datos para comparar

            $acumuladoGenerado = floatval($this->TotalComprobante) * (((Configuration::first()?->porc_accumulated) ?? 0) / 100);
        }

        if ($accumulated) {
            $createAccumulatedTransaction($accumulated, $action, $acumuladoGenerado * $multiple, $this, $this->clinic?->name);
        }
    }

    public function generateAccumulatedTweakNotaCredito($rollback)
    {
        $createAccumulatedTransaction = resolve(CreateAccumulatedTransaction::class);
        $multiple = $rollback ? 1 : -1;
        $action = $rollback ? 'Rollback Nota Credito Generacion' : 'Nota Credito Generacion';
        $referencia = $this->referencias()->latest()->first();
        $originalInvoice = $referencia ? $referencia->originalInvoice : null;
        $accumulated = Accumulated::find($this->affiliation_id);

        if ($referencia && $originalInvoice) {

            $originalAcumuladoGenerado= floatval($originalInvoice->TotalComprobante) * (((Configuration::first()?->porc_accumulated) ?? 0) / 100);

            $currentAcumuladoGenerado = floatval($this->TotalComprobante) * (((Configuration::first()?->porc_accumulated) ?? 0) / 100);

            if ($originalAcumuladoGenerado != $currentAcumuladoGenerado) {
                $acumuladoGenerado = $originalAcumuladoGenerado - $currentAcumuladoGenerado;
            } else {
                $acumuladoGenerado = $currentAcumuladoGenerado;
            }
        } else { // no exite referencia en base de datos para comparar

            $acumuladoGenerado = floatval($this->TotalComprobante) * (((Configuration::first()?->porc_accumulated) ?? 0) / 100);
        }

        if ($accumulated) {
            $createAccumulatedTransaction($accumulated, $action, $acumuladoGenerado * $multiple, $this, $this->clinic?->name);
        }
    }

    public function rollbackCommissionTweak()
    {
        $this->commissionTweak(true);
    }

    public function commissionTweak($rollback = false)
    {
        if(!$this->user?->commission_affiliation){
            return;
        }

        if ($this->TipoDocumento == '01' || $this->TipoDocumento == '04') { //factura y tiquete
            $this->commissionTweakFacturaTiquete($rollback);
        }

        if ($this->TipoDocumento == '02') {  //nota debito
            $this->commissionTweakNotaDebito($rollback);
        }

        if ($this->TipoDocumento == '03') {  //nota credito
            $this->commissionTweakNotaCredito($rollback);
        }
    }
    public function commissionTweakFacturaTiquete($rollback)
    {
        $createCommissionTransaction = resolve(CreateCommissionTransaction::class);

        if ($rollback) {

            CommissionTransaction::where('resource_type', get_class($this))
                ->where('resource_id', $this->id)
                ->where('user_id', $this->user_id)
                ->whereNull('paid_at')
                ->delete();
        } else {
            $createCommissionTransaction($this->user_id, $this->getTotalCommission(), $this);
        }
    }

    public function commissionTweakNotaDebito($rollback)
    {
        $createCommissionTransaction = resolve(CreateCommissionTransaction::class);
        $referencia = $this->referencias()->latest()->first();
        $originalInvoice = $referencia ? $referencia->originalInvoice : null;

        if ($referencia && $originalInvoice) {

            $userId = $originalInvoice->user_id;
            $TotalCommission = $originalInvoice->getTotalCommission();
            $resource =  $originalInvoice;
        } else { // no exite referencia en base de datos para comparar

            $userId = $this->user_id;
            $TotalCommission = $this->getTotalCommission();
            $resource =  $this;
        }

        if ($rollback) {
            CommissionTransaction::where('resource_type', get_class($this))
                ->where('resource_id', $this->id)
                ->where('user_id', $this->user_id)
                ->whereNull('paid_at')
                ->delete();

            $createCommissionTransaction($userId, $TotalCommission, $resource);
        } else {
            CommissionTransaction::where('resource_type', get_class($resource))
                ->where('resource_id', $resource->id)
                ->where('user_id', $userId)
                ->whereNull('paid_at')
                ->delete();

            if ($resource->TipoDocumento == '01' || $resource->TipoDocumento == '04') { // se crear la comision si la factura original es otra factura. Si es una nota de credito no hay que crearla ya que se estaria anulando
                $createCommissionTransaction($this->user_id, $this->getTotalCommission(), $this);
            }
        }
    }

    public function commissionTweakNotaCredito($rollback)
    {
        $createCommissionTransaction = resolve(CreateCommissionTransaction::class);
        $referencia = $this->referencias()->latest()->first();
        $originalInvoice = $referencia ? $referencia->originalInvoice : null;


        if ($referencia && $originalInvoice) {

            $userId = $originalInvoice->user_id;
            $TotalCommission = $originalInvoice->getTotalCommission();
            $resource =  $originalInvoice;
        } else { // no exite referencia en base de datos para comparar

            $userId = $this->user_id;
            $TotalCommission = $this->getTotalCommission();
            $resource =  $this;
        }

        if ($rollback) {
            CommissionTransaction::where('resource_type', get_class($this))
                ->where('resource_id', $this->id)
                ->where('user_id', $this->user_id)
                ->whereNull('paid_at')
                ->delete();

            $createCommissionTransaction($userId, $TotalCommission, $resource);
        } else {

            CommissionTransaction::where('resource_type', get_class($resource))
                ->where('resource_id', $resource->id)
                ->where('user_id', $userId)
                ->whereNull('paid_at')
                ->delete();

            if ($resource->TotalWithNota > 0) {

                $createCommissionTransaction($this->user_id, $this->getTotalCommission(), $this);
            }
        }
    }
}
