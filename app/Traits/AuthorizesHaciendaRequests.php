<?php

namespace App\Traits;

use App\Services\HaciendaAuthenticationService;

trait AuthorizesHaciendaRequests
{
    /**
     * Resolves the elements to send when authorazing the request
     * @param  array &$queryParams
     * @param  array &$formParams
     * @param  array &$headers
     * @return void
     */
    public function resolveAuthorization(&$queryParams, &$formParams, &$headers, $obligadoTributario)
    {
        $accessToken = $this->resolveAccessToken($obligadoTributario);

        $headers['Authorization'] = $accessToken;
        //$headers['Content-Type'] = 'application/json';
      
    }

    public function resolveAccessToken($obligadoTributario)
    {
        $authenticationService = resolve(HaciendaAuthenticationService::class);

        if (auth()->user() || $obligadoTributario) {
            return $authenticationService->getAuthenticatedUserToken($obligadoTributario);
        }

        return '';
    }
}
