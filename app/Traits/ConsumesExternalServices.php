<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait ConsumesExternalServices
{
    /**
     * Send a request to any service
     * @return string
     */
    public function makeRequest($method, $requestUrl, $queryParams = [], $formParams = [], $headers = [], $hasFile = false, $json = false, $obligadoTributario = null)
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        if (method_exists($this, 'resolveAuthorization')) {
            $this->resolveAuthorization($queryParams, $formParams, $headers, $obligadoTributario);
        }

        $bodyType = $json ? 'json' : 'form_params'; // or form_params
        
        if ($hasFile) {
            $bodyType = 'multipart';

            $multipart = [];

            foreach ($formParams as $name => $contents) {
                $multipart[] = ['name' => $name, 'contents' => $contents];
            }
        }
       
        try {
            $response = $client->request($method, $requestUrl, [
                'query' => $queryParams,
                $bodyType => $hasFile ? $multipart : $formParams,
                'headers' => $headers

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

            // if ($e->getResponse()->getStatusCode() == 404) {
            //     throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error de conexion con hacienda');
            // } else {

            //     if ($e->getResponse()->hasHeader('X-Error-Cause')) {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. ' . $e->getResponse()->getHeader('X-Error-Cause')[0]);
            //     } else {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error Hacienda no identificado');
            //     }

            // }
        }

       
    }

    /**
     * Send a request to any service
     * @return string
     */
    public function makeRequestTokens($method, $requestUrl, $queryParams = [], $formParams = [], $headers = [])
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);
        
        if (method_exists($this, 'resolveAuthorization')) {
            $this->resolveAuthorization($queryParams, $formParams, $headers);
        }

        $bodyType = 'form_params'; // or form_params


        try {
            $response = $client->request($method, $requestUrl, [
                'query' => $queryParams,
                $bodyType => $formParams,
                'headers' => $headers

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

                return false;

            

            
        }
    }
}
