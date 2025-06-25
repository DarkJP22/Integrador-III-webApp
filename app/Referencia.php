<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Referencia extends Model
{
    protected $table = 'referencias';
    protected $guarded = ['tipo_documento_name', 'codigo_referencia_name'];

    protected $appends = ['tipo_documento_name', 'codigo_referencia_name'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($referencia) {

            $originalInvoice = $referencia->originalInvoice;

            if ($originalInvoice) { // se hace esta comprobacion por que la referencia  a la factura original puede no existir (contingencia)
                $invoice = $referencia->invoice;
                $montoOriginal = $originalInvoice->TotalComprobante;

                if ($invoice->TipoDocumento == '02') { // nota debito
                    $montoOriginal = $montoOriginal + ($invoice->TotalComprobante - $montoOriginal);
                }
                if ($invoice->TipoDocumento == '03') { // nota credito

                    $montoOriginal = ($montoOriginal - $invoice->TotalComprobante) > 0 ? $montoOriginal - ($montoOriginal - $invoice->TotalComprobante) : 0;
                }

                $originalInvoice->update(['TotalWithNota' => $montoOriginal]);
                $originalInvoice->calculatePendingAmount();
            }



        });
    }

    public function getTipoDocumentoNameAttribute()
    {

        return trans('utils.tipo_documento.' . $this->TipoDocumento);
    }

    public function getCodigoReferenciaNameAttribute()
    {

        return trans('utils.codigo_referencia.' . $this->CodigoReferencia);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function originalInvoice()
    {
        return $this->belongsTo(Invoice::class, 'referencia_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
