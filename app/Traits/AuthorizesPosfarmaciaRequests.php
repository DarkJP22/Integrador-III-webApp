<?php

namespace App\Traits;



trait AuthorizesPosfarmaciaRequests
{
    /**
     * Resolves the elements to send when authorazing the request
     * @param  array &$queryParams
     * @param  array &$formParams
     * @param  array &$headers
     * @return void
     */
    public function resolveAuthorization(&$queryParams, &$formParams, &$headers, $accessToken)
    {
        //$accessToken = $this->resolveAccessToken($obligadoTributario);

        $headers['Authorization'] = 'Bearer '. $accessToken;
        //$headers['Accept'] = 'application/json';
        //$headers['Content-Type'] = 'application/json';
      
    }

    // public function resolveAccessToken($patient)
    // {
    //     $authenticationService = resolve(HaciendaAuthenticationService::class);

    //     if (auth()->user() || $obligadoTributario) {
    //         return $authenticationService->getAuthenticatedUserToken($obligadoTributario);
    //     }

    //     return '';
    // }
}
