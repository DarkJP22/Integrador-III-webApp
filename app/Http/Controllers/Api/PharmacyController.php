<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Pharmacy;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    /**
     * Display a listing of all pharmacies.
     * Optionally filter by distance from user coordinates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search['q'] = $request->get('q');
        $userLat = $request->get('lat');
        $userLon = $request->get('lon');
        $maxDistance = $request->get('max_distance'); // en kilómetros
        
        $pharmacies = Pharmacy::when($search['q'], function($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
        })->get();

        // Si se proporcionan coordenadas y distancia máxima, filtrar por distancia
        if ($userLat && $userLon && $maxDistance) {
            $pharmacies = $pharmacies->filter(function($pharmacy) use ($userLat, $userLon, $maxDistance) {
                // Solo calcular distancia si la farmacia tiene coordenadas
                if ($pharmacy->lat && $pharmacy->lon) {
                    $distance = $this->haversineDistance($userLat, $userLon, $pharmacy->lat, $pharmacy->lon);
                    return $distance <= $maxDistance;
                }
                return false; // Excluir farmacias sin coordenadas
            })->map(function($pharmacy) use ($userLat, $userLon) {
                // Agregar la distancia calculada a cada farmacia
                $pharmacy->distance = $this->haversineDistance($userLat, $userLon, $pharmacy->lat, $pharmacy->lon);
                return $pharmacy;
            })->sortBy('distance')->values(); // Ordenar por distancia más cercana
        }

        return response()->json([
            'pharmacies' => $pharmacies,
            'search' => $search,
            'filters' => [
                'lat' => $userLat,
                'lon' => $userLon,
                'max_distance' => $maxDistance
            ]
        ]);
    }

    /**
     * Calculate distance between two points using Haversine formula
     * 
     * @param float $lat1 Latitude of point 1
     * @param float $lon1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lon2 Longitude of point 2
     * @return float Distance in kilometers
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371; // Radio de la Tierra en kilómetros
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $R * $c; // Distancia en kilómetros
    }
}
