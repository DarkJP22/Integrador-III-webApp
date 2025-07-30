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
            'type_affiliation' => ['required', Rule::in(['Basic', 'Standard', 'Premium'])],
            'voucher' => 'required|file|mimes:jpg,jpeg,png,pdf',
        ]);
        $validated['user_id'] = auth()->id();
        $validated['active'] = $request->input('active', false);
        if ($validated['type_affiliation'] === 'Basic') {
            $validated['discount'] = 5; // Descuento del 5% para afiliación básica
        } elseif ($validated['type_affiliation'] === 'Standard') {
            $validated['discount'] = 10; // Descuento del 10% para afiliación estándar
        } elseif ($validated['type_affiliation'] === 'Premium') {
            $validated['discount'] = 15; // Descuento del 15% para afiliación premium
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

    // Actualizar usuario de afiliación
    public function update(Request $request, AffiliationUsers $affiliationUsers)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'active' => 'boolean',
            'type_affiliation' => 'required|enum:Basic,Standard,Premium',
            'voucher' => 'required|file|mimes:jpg,jpeg,png,pdf',
        ]);

   if ($request->hasFile('voucher')) {
        // Guarda en storage/app/public/vouchers
        $path = $request->file('voucher')->store('vouchers', 'public');
        $validated['voucher'] = $path;
       
    }
        $affiliationUsers->fill($validated);
        $affiliationUsers->save();

        if ($request->wantsJson()) {
            return response($affiliationUsers, 200);
        }
        return response()->json(['message' => 'Usuario de afiliación actualizado correctamente'], 200);
    }

    // Eliminar usuario de afiliación
    public function destroy(AffiliationUsers $affiliationUsers)
    {
        $result = $affiliationUsers->delete();

        if (request()->wantsJson()) {
            if ($result === true) {
                return response([], 204);
            }
            return response(['message' => 'Error al eliminar'], 422);
        }
        return response()->json(['message' => 'Usuario de afiliación eliminado correctamente'], 200);
    }

   public function checkUserAffiliation($userId)
{
    // Buscar afiliaciones activas del usuario
    $activeAffiliation = AffiliationUsers::where('user_id', $userId)
        ->where('active', true)
        ->first();

    if ($activeAffiliation) {
        return  affiliationUsers::where('user_id', $userId)
            ->where('active', true)
            ->first();
    } else {
        return AffiliationUsers::where('user_id', $userId)
            ->where('active', false)
            ->first();
    }
}
}