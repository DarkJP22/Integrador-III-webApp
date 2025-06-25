<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait ConsumesPosfarmaciaServices
{
    /**
     * Send a request to any service
     * @return string
     */
    public function makeRequest($method, $requestUrl, $queryParams = [], $formParams = [], $headers = [], $accessToken = null)
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        if (method_exists($this, 'resolveAuthorization')) {
            
            $this->resolveAuthorization($queryParams, $formParams, $headers, $accessToken);
           
        }

        $bodyType = 'form_params'; // or form_params
       //dd($client);
        try {
            $response = $client->request($method, $requestUrl, [
                 'query' => $queryParams,
                  $bodyType => $formParams,
                 'headers' => $headers,

            ]);
        
            

            $response = $response->getBody()->getContents();

           
            if (method_exists($this, 'decodeResponse')) {
                $response = $this->decodeResponse($response);
            }

            if (method_exists($this, 'checkIfErrorResponse')) {
                $this->checkIfErrorResponse($response);
            }

            return $response;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            
            \Log::error(\GuzzleHttp\Psr7\Message::toString($e->getResponse()));

            if ($e->getResponse()->hasHeader('X-Error-Cause')) {

                //throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. ' . $e->getResponse()->getHeader('X-Error-Cause')[0]);

                return $e->getResponse()->getHeader('X-Error-Cause')[0];

            }

         
        } catch (\GuzzleHttp\Exception\ServerException $e){

            \Log::error(\GuzzleHttp\Psr7\Message::toString($e->getResponse()));
            return $e->getResponse()->getStatusCode();
        }

       
    }

   
}
