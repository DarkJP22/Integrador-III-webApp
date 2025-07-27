<?php

namespace App\Http\Controllers\Api;

use App\Drug;
use App\Enums\DrugStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DrugController extends Controller
{
    /**
     * Display a listing of drugs.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Drug::query();

            // Filter by published status only
            $query->where('status', DrugStatus::PUBLISHED);

            // Search functionality
            if ($request->has('q') && $request->filled('q')) {
                $search = ['q' => $request->input('q')];
                $query->search($search);
            }

            // Filter by presentation if provided
            if ($request->has('presentation') && $request->filled('presentation')) {
                $query->where('presentation', $request->input('presentation'));
            }

            // Filter by laboratory if provided
            if ($request->has('laboratory') && $request->filled('laboratory')) {
                $query->where('laboratory', 'like', '%' . $request->input('laboratory') . '%');
            }

            // Sorting
            $sortField = $request->input('sort', 'name');
            $sortOrder = $request->input('order', 'asc');
            
            // Validate sort fields to prevent SQL injection
            $allowedSortFields = ['name', 'laboratory', 'presentation', 'created_at', 'updated_at'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            } else {
                $query->orderBy('name', 'asc');
            }

            // Get all drugs
            $drugs = $query->get();

            return response()->json([
                'success' => true,
                'data' => $drugs,
                'total' => $drugs->count(),
                'available_presentations' => Drug::PRESENTATIONS,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los medicamentos',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Display the specified drug.
     * 
     * @param Drug $drug
     * @return JsonResponse
     */
    public function show(Drug $drug): JsonResponse
    {
        try {
            // Only show published drugs
            if ($drug->status !== DrugStatus::PUBLISHED) {
                return response()->json([
                    'success' => false,
                    'message' => 'Medicamento no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $drug->load('creator:id,name')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el medicamento',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Get available presentations.
     * 
     * @return JsonResponse
     */
    public function presentations(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Drug::PRESENTATIONS
        ], 200);
    }

    /**
     * Search drugs by name or laboratory.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);

        try {
            $query = Drug::query();
            
            // Only published drugs
            $query->where('status', DrugStatus::PUBLISHED);

            // Apply search
            $search = ['q' => $request->input('q')];
            $query->search($search);

            // Limit results for search
            $drugs = $query->orderBy('name', 'asc')->limit(20)->get();

            return response()->json([
                'success' => true,
                'data' => $drugs,
                'total' => $drugs->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }
}
