<?php

namespace App\Http\Controllers\Pharmacy\Orders_Pharmacy\WEB;

use App\Http\Controllers\Controller;
use App\Orders;
use App\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Verificar que el usuario tenga acceso a la farmacia de la orden
     */
    private function validatePharmacyAccess(Orders $order = null)
    {
        $pharmacyId = Auth::user()->pharmacy->id ?? null;

        if (!$pharmacyId) {
            abort(403, 'No tienes una farmacia asociada.');
        }

        if ($order && $order->pharmacy_id !== $pharmacyId) {
            abort(403, 'No autorizado para acceder a esta orden.');
        }

        return $pharmacyId;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validar acceso y obtener farmacia
        $pharmacyId = $this->validatePharmacyAccess();

        // Construir query con filtros
        $query = Orders::with(['user', 'pharmacy'])
            ->where('pharmacy_id', $pharmacyId);

        // Filtro de búsqueda
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->where('consecutive', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Obtener órdenes paginadas - más recientes primero (por ID descendente)
        $orders = $query->orderBy('id', 'desc')->paginate(10);

        // Formatear fechas para todas las órdenes
        $orders->getCollection()->transform(function ($order) {
            $order->formatted_date = $order->date ? \Carbon\Carbon::parse($order->date)->format('d/m/Y H:i') : 'N/A';
            return $order;
        });

        return view('pharmacy.requests-orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     
    public function create()
    {
        // Las órdenes se crean desde la app móvil
        return view('pharmacy.requests-orders.create');
    }
    */

    /**
     * Store a newly created resource in storage.
     
    public function store(Request $request)
    {
        $validated = $request->validate([
            'consecutive' => 'required|string|max:255',
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|string|max:50',
            'payment_method' => 'required|boolean',
            'requires_shipping' => 'required|boolean',
            'address' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lot' => 'nullable|numeric',
            'order_total' => 'required|numeric',
            'shipping_total' => 'nullable|numeric',
        ]);

        Orders::create($validated);
        return redirect()->route('pharmacy.orders.index')->with('success', 'Orden creada.');
    }
    */

    /**
     * Display the specified resource.
    public function show(Orders $order)
    {
        $this->validatePharmacyAccess($order);

        return view('pharmacy.requests-orders.show', compact('order'));
    }
    */

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orders $order)
    {
        $this->validatePharmacyAccess($order);

        // Cargar detalles de la orden y el medicamento relacionado
        $order->load('details.drug');

        return view('pharmacy.requests-orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orders $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:cotizacion,esperando_confirmacion,confirmado,preparando,cancelado,despachado',
            'shipping_cost' => 'nullable|numeric|min:0',
            'details' => 'array',
            'details.*.quantity_available' => 'required|numeric|min:0',
            'details.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Verificar que el usuario puede editar esta orden
        $this->validatePharmacyAccess($order);

        // Usar transacción para garantizar consistencia
        DB::transaction(function () use ($request, $order, $validated) {
            $subtotal = 0;

            // Actualizar detalles de la orden
            foreach ($request->input('details', []) as $id => $detailData) {
                $detail = OrderDetail::find($id);
                if ($detail && $detail->order_id === $order->id) {
                    $cantidadDisponible = floatval($detailData['quantity_available']);
                    $cantidadSolicitada = $detail->requested_amount;
                    $precio = floatval($detailData['unit_price']);

                    // Aplicar la misma lógica que en JavaScript:
                    // Si cantidad solicitada > disponible, usar disponible
                    // Si cantidad solicitada <= disponible, usar solicitada
                    $cantidadParaCalculo = $cantidadSolicitada > $cantidadDisponible ? $cantidadDisponible : $cantidadSolicitada;

                    $subtotalDetalle = $cantidadParaCalculo * $precio;

                    $detail->update([
                        'quantity_available' => $cantidadDisponible,
                        'unit_price' => $precio,
                        'products_total' => $subtotalDetalle,
                    ]);

                    $subtotal += $subtotalDetalle;
                }
            }

            // Calcular totales
            $totalConIVA = $subtotal * 1.13;
            $shippingCost = floatval($request->input('shipping_cost', 0));
            $totalConEnvio = $totalConIVA + $shippingCost;

            // Actualizar la orden
            $order->update([
                'status' => $validated['status'],
                'order_total' => $totalConIVA,
                'shipping_total' => $totalConEnvio,
            ]);
        });

        return redirect()->route('pharmacy.orders.index')->with('success', 'Orden actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     
    public function destroy(Orders $order)
    {
        $this->validatePharmacyAccess($order);

        $order->delete();
        return redirect()->route('pharmacy.orders.index')->with('success', 'Orden eliminada.');
    }
    */
}
