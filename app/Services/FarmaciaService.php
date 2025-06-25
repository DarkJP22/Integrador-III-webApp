<?php

namespace App\Services;

use App\Patient;
use App\Pharmacy;
use App\Traits\InteractsWithHaciendaResponses;
use App\Traits\AuthorizesPosfarmaciaRequests;
use App\Traits\ConsumesPosfarmaciaServices;
use Illuminate\Support\Arr;

class FarmaciaService
{
    use ConsumesPosfarmaciaServices, AuthorizesPosfarmaciaRequests, InteractsWithHaciendaResponses;

    /**
     * The url from which send the requests
     * @var string
     */
    protected $baseUri;

    public function __construct()
    {
        $this->baseUri = '';
       
    }

    /**
     * Obtains the list of products from the API
     * @return stdClass
     */
    public function getHistorialCompras(Patient $patient, $pharmacyId, $start, $end = null)
    {
        $data = [];

        if(!$patient->ide){
            return $data;
        }
       

        if($pharmacyId){
            $credentials = $patient->apipharmacredentials()->where('pharmacy_id', $pharmacyId)->get();
        }else{
            $credentials = $patient->apipharmacredentials;
        }
       
        
        $queryParams = [
            'start'=> $start
        ];

        if($end){
            $queryParams['end'] = $end;
        }
       
      
        foreach ($credentials as $credential) {
          $this->baseUri = $credential->api_url;
          
          
          $compras = $this->makeRequest('GET', 'api/customers/'.$patient->ide.'/invoices', $queryParams , [] , [], $credential->access_token);

          $pharmacy = Pharmacy::find($credential->pharmacy_id);
          
          foreach ($compras as $compra) {
            $compra->pharmacy = $pharmacy->name;
          }

         // $data = array_merge($data, $compras);
          // para pruebas
          $compras = [
                [
                    'Codigo' => "4968420508550",
                    'Detalle' => "GUANTE NITRILO NIPRO L",
                    'ExistenciaFrac' => 49.00,
                    'ExistenciaUnid' => 1.00,
                    'TotalFracciones' => 0.000,
                    'TotalUnidades' => 5.000,
                    'created_at' => "2019-08-26 15:30:32",
                    'pharmacy' => "Otra farmacia",
                ],
                [
                    'Codigo' => "4968420508550",
                    'Detalle' => "GUANTE NITRILO NIPRO BB",
                    'ExistenciaFrac' => 49.00,
                    'ExistenciaUnid' => 1.00,
                    'TotalFracciones' => 0.000,
                    'TotalUnidades' => 2.000,
                    'created_at' => "2019-08-26 15:30:32",
                    'pharmacy' => "Otra farmacia",
                ]
            ];
         
          $data = array_merge($data, $compras);

        }
       
        
        return $data;
    }

    public function disablePOS(Pharmacy $pharmacy)
    {
      

        if($pharmacy && $pharmacy->pharmacredential){
            
            $this->baseUri = $pharmacy->pharmacredential->api_url;
          
          
            return $this->makeRequest('POST', 'api/settings/disable', [], [] , [], $pharmacy->pharmacredential->access_token);

        }

    }

   

  

    
}
