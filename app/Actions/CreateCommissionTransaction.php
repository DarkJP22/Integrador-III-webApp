<?php
namespace App\Actions;

use App\CommissionTransaction;

class CreateCommissionTransaction{

    public function __invoke($userId, float $monto, $resource = null): void
    {

        if($monto > 0){

            CommissionTransaction::create([
                'user_id' => $userId,
                'resource_id' => $resource?->id,
                'resource_type' => $resource ? get_class($resource) : null,
                'Total' => $monto,
                'CodigoMoneda' => $resource?->CodigoMoneda ?? 'CRC'

            ]);
            
        }
           

        
    }

}
