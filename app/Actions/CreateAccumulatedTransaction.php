<?php
namespace App\Actions;

use App\Accumulated;

class CreateAccumulatedTransaction{

    public function __invoke(Accumulated $accumulated, string $action, float $acumuladoUtilizado, $resource = null, $place = null): void
    {
    
            $acumulado = $accumulated->acumulado;
            $nuevoAcumulado = ($acumulado + $acumuladoUtilizado);

            $accumulated->transactions()->create([
                'resource_id' => $resource?->id,
                'resource_type' => $resource ? get_class($resource) : null,
                'MontoTransaccion' => $acumuladoUtilizado,
                'AcumuladoAntesTransaccion' => $acumulado,
                'AcumuladoDespuesTransaccion' => $nuevoAcumulado,
                'action' => $action,
                'CodigoMoneda' => $resource?->CodigoMoneda ?? 'CRC',
                'place' => $place

            ]);

            $accumulated->update([
                'acumulado' => $nuevoAcumulado//($nuevoAcumulado < 0) ? 0 : $nuevoAcumulado
            ]);
        
    }

}
