<?php

namespace App\Http\Controllers\Api;
use Illuminate\Validation\Rule;
use App\Affiliation;
use Illuminate\Http\Request;
use App\AffiliationUsers;
use App\User;
use App\Http\Controllers\Controller;

use function Illuminate\Log\log;

class AffiliationUsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Listar usuarios de afiliación
    public function index()
    {
        return AffiliationUsers::all();
    }

    // Crear un nuevo usuario de afiliación
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_affiliation' => ['required', Rule::in(['Monthly', 'Semi-Annually', 'Annually'])],
            'voucher' => 'required|file|mimes:jpg,jpeg,png,pdf',
        ]);
        $validated['user_id'] = auth()->id();
         $validated['id'] = $this->generateAffiliationId();
        $validated['active'] = $request->input('active', 'Pending');
        if ($validated['type_affiliation'] === 'Monthly') {
            $validated['priceToAffiliation'] = 2500; // Precio de afiliación mensual
        } elseif ($validated['type_affiliation'] === 'Semi-Annually') {
            $validated['priceToAffiliation'] = 10000; // Precio de afiliación semestral
        } elseif ($validated['type_affiliation'] === 'Annually') {
            $validated['priceToAffiliation'] = 30000; // Precio de afiliación anual
        }
        if ($request->hasFile('voucher')) {
            // Guarda en storage/app/public/vouchers
            $path = $request->file('voucher')->store('vouchers', 'public');
            $validated['voucher'] = $path;
        } else {
            $validated['voucher'] = null; 
        }
        $AffiliationUsers = AffiliationUsers::create($validated);

        if ($request->wantsJson()) {
            return response($AffiliationUsers, 201);
        }
        // Cambia el redirect por un response normal si es API
        return response()->json(['message' => 'Usuario de afiliación creado correctamente'], 201);
    }


   public function checkUserAffiliation($userId)
{
    // Buscar afiliaciones activas del usuario
    $activeAffiliation = AffiliationUsers::where('user_id', $userId)->first();

    if (!$activeAffiliation) {
        return response()->json(['message' => 'No tienes una afiliación activa'], 404);
    }

    if ($activeAffiliation->active === "Approved") {
        return $activeAffiliation;
    } elseif ($activeAffiliation->active === "Denied") {
        return $activeAffiliation;
    }else {
        return $activeAffiliation;
    }
}


private function generateAffiliationId()
{
    $last = AffiliationUsers::orderBy('created_at', 'desc')->first();

    if (!$last) {
        return 'Affi-00001';
    }

 
    $lastNumber = (int) substr($last->id, 5);
    $nextNumber = $lastNumber + 1;

    return 'Affi-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
}

}