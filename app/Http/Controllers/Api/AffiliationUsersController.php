<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\AffiliationUsers;
use App\Users;
use App\Http\Controllers\Controller;

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
            'date' => 'required|date',
            'active' => 'boolean',
            'type_affiliation' => 'required|integer',
            'voucher' => 'required',
        ]);
        $validated['user_id'] = auth()->id();
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
            'date' => 'required|date',
            'active' => 'boolean',
            'type_affiliation' => 'required|integer',
            'voucher' => 'required',
        ]);

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
}

