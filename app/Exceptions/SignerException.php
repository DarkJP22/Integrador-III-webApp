<?php

namespace App\Exceptions;

use Exception;

class SignerException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report($message)
    {
        \Log::error($message);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
                return response()->json(['message' => 'Error al firmar el xml de la factura. Intentalo de nuevo buscando la factura creada y dandole enviar a hacienda o ponte en contacto con el proveedor!!'], 500);
        }
    }
}
