<?php

namespace App\Traits;

use function GuzzleHttp\json_decode;
use Illuminate\Support\Str;

trait InteractsWithHaciendaResponses
{
    /**
     * Decode correspondingly the response
     * @param  array $response
     * @return stdClass
     */
    public function decodeResponse($response)
    {
        
        $decodedResponse = $response ? json_decode($response) : $response;
       
        return $decodedResponse->data ?? $decodedResponse;
    }

    /**
     * Resolve if the request to the service failed
     * @param  array $response
     * @return void
     */
    public function checkIfErrorResponse($response)
    {
        
        if (isset($response->error)) {
            throw new \Exception("Something failed: {$response->error}");
        }

    }
}
