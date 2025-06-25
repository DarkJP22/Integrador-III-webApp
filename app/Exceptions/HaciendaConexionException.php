<?php

namespace App\Exceptions;

use Exception;

class HaciendaConexionException extends Exception
{
     public $message;
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report($message)
    {
        \Log::error($message);
        $this->message = $message;
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
                return response()->json(['message' =>  $this->message], 504);
            }
    }
}
