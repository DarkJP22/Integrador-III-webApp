<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliationPayment extends Model
{
    protected $guarded = [];
    protected $table = 'affiliation_payments';

    protected static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
            $payment->updateAcumulado($payment, 'created', true);
           
        });
        
        static::deleting(function ($payment) {
            $payment->updateAcumulado($payment, 'deleting', false);
           
        });

    }
    public function updateAcumulado($payment, $action, $sumar = true)
    {

            $affiliation = $payment->affiliation;
            $acumulado = $affiliation->acumulado;
            $nuevoAcumulado = $sumar ? ($acumulado + $payment->amount) : ($acumulado - $payment->amount);
           
            $affiliation->transactions()->create([
                'transactable_id' => $payment->id,
                'transactable_type' => get_class($payment),
                'MontoTransaccion' => $payment->amount,
                'AcumuladoAntesTransaccion' => $acumulado,
                'AcumuladoDespuesTransaccion' => $nuevoAcumulado,
                'action' => $action

            ]);

            $affiliation->update([
                'acumulado' => $nuevoAcumulado
            ]);

    }

    public function affiliation()
    {
        return $this->belongsTo(Affiliation::class);
    }

    /**
     * Get all of the post's comments.
     */
    public function transaction()
    {
        return $this->morphOne(AffiliationTransaction::class, 'transactable');
    }
    
}
