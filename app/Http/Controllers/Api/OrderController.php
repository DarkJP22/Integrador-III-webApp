<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Orders;
use App\OrderDetail;
use App\Drug;
use App\Pharmacy;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Notifications\NewOrderPharmacie;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     * GET /api/orders
     */
    public function index(Request $request)
    {
        $search = $request->only(['q', 'status', 'pharmacy_id', 'user_id', 'payment_method']);
        
        $orders = Orders::with([
            'user:id,name,email,phone_number',
            'pharmacy:id,name,address,phone',
            'details:id,order_id,drug_id,requested_amount,quantity_available,unit_price,products_total,description',
            'details.drug:id,name,description'
        ])
        ->when($search['q'] ?? null, function($query, $search) {
            return $query->where('consecutive', 'like', "%{$search}%")
                        ->orWhereHas('user', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
        })
        ->when($search['status'] ?? null, function($query, $status) {
            return $query->where('status', $status);
        })
        ->when($search['pharmacy_id'] ?? null, function($query, $pharmacyId) {
            return $query->where('pharmacy_id', $pharmacyId);
        })
        ->when($search['user_id'] ?? null, function($query, $userId) {
            return $query->where('user_id', $userId);
        })
        ->when(isset($search['payment_method']), function($query) use ($search) {
            return $query->where('payment_method', $search['payment_method']);
        })
        ->orderBy('date', 'desc')
        ->paginate(15);

        return response()->json([
            'orders' => $orders,
            'search' => $search
        ]);
    }

    /**
     * Store a newly created order.
     * POST /api/orders
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'user_id' => 'required|exists:users,id',
            'payment_method' => 'boolean',
            'requires_shipping' => 'boolean',
            'address' => 'nullable|string|max:500',
            'lat' => 'nullable|numeric|between:-90,90',
            'lot' => 'nullable|numeric|between:-180,180',
            'shipping_total' => 'nullable|numeric|min:0',
            'details' => 'required|array|min:1',
            'details.*.drug_id' => 'required|exists:drugs,id',
            'details.*.requested_amount' => 'required|integer|min:1',
            'details.*.unit_price' => 'nullable|numeric|min:0',
            'details.*.description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            // Generar consecutivo único
            $consecutive = $this->generateConsecutive();
            
            // Crear la orden principal
            $order = Orders::create([
                'consecutive' => $consecutive,
                'pharmacy_id' => $request->pharmacy_id,
                'user_id' => $request->user_id,
                'date' => now(),
                'status' => 'cotizacion',
                'payment_method' => $request->payment_method ?? false,
                'requires_shipping' => $request->requires_shipping ?? false,
                'address' => $request->address,
                'lat' => $request->lat,
                'lot' => $request->lot,
                'order_total' => 0,
                'shipping_total' => $request->shipping_total ?? 0,
            ]);

            $totalProducts = 0;

            // Crear los detalles de la orden
            foreach ($request->details as $detail) {
                $orderDetail = OrderDetail::create([
                    'order_id' => $order->id,
                    'drug_id' => $detail['drug_id'],
                    'requested_amount' => $detail['requested_amount'],
                    'quantity_available' => 0, // Inicialmente 0, la farmacia lo actualiza
                    'unit_price' => $detail['unit_price'] ?? 0,
                    'description' => $detail['description'] ?? null,
                    'products_total' => 0 // Se calculará cuando farmacia confirme precios
                ]);

                $totalProducts += $orderDetail->products_total;
            }

            // Actualizar total de la orden
            $order->update([
                'order_total' => $totalProducts + $order->shipping_total
            ]);

            // Cargar relaciones para respuesta
            $order->load([
                'user:id,name,email,phone_number',
                'pharmacy:id,name,address,phone',
                'details.drug:id,name,description'
            ]);

            // Notificar a todos los usuarios de la farmacia sobre la nueva orden
            $pharmacy = Pharmacy::with('users')->find($request->pharmacy_id);
            if ($pharmacy && $pharmacy->users->count() > 0) {
                foreach ($pharmacy->users as $pharmacyUser) {
                    $pharmacyUser->notify(new NewOrderPharmacie($order));
                }
            }
            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order
            ], 201);
        });
    }

    /**
     * Display the specified order.
     * GET /api/orders/{id}
     */
    public function show(Orders $order)
    {
        $order->load([
            'user:id,name,email,phone_number',
            'pharmacy:id,name,address,phone,lat,lon',
            'details:id,order_id,drug_id,requested_amount,quantity_available,unit_price,products_total,description',
            'details.drug:id,name,description'
        ]);

        return response()->json([
            'order' => $order
        ]);
    }

    /**
     * Update the specified order.
     * PUT /api/orders/{id}
     */
    public function update(Request $request, Orders $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => [
                'string',
                Rule::in(['cotizacion', 'esperando_confirmacion', 'confirmado', 'preparando', 'cancelado', 'despachado'])
            ],
            'shipping_total' => 'nullable|numeric|min:0',
            'voucher' => 'nullable|string',
            'details' => 'sometimes|array',
            'details.*.id' => 'required|exists:order_details,id',
            'details.*.quantity_available' => 'nullable|integer|min:0',
            'details.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        return DB::transaction(function () use ($request, $order) {
            // Actualizar campos de la orden
            $orderData = $request->only(['status', 'shipping_total', 'voucher']);
            if (!empty($orderData)) {
                $order->update($orderData);
            }

            // Actualizar detalles si se proporcionan
            if ($request->has('details')) {
                $totalProducts = 0;
                
                foreach ($request->details as $detailData) {
                    $detail = OrderDetail::findOrFail($detailData['id']);
                    
                    // Verificar que el detalle pertenece a esta orden
                    if ($detail->order_id !== $order->id) {
                        return response()->json([
                            'error' => 'Order detail does not belong to this order'
                        ], 403);
                    }

                    $updateData = [];
                    if (isset($detailData['quantity_available'])) {
                        $updateData['quantity_available'] = $detailData['quantity_available'];
                    }
                    if (isset($detailData['unit_price'])) {
                        $updateData['unit_price'] = $detailData['unit_price'];
                    }

                    if (!empty($updateData)) {
                        $detail->update($updateData);
                        
                        // Recalcular products_total
                        $cantidad = min($detail->requested_amount, $detail->quantity_available);
                        $detail->update([
                            'products_total' => $cantidad * $detail->unit_price
                        ]);
                    }

                    $totalProducts += $detail->products_total;
                }

                // Actualizar total de la orden
                $order->update([
                    'order_total' => $totalProducts + $order->shipping_total
                ]);
            }

            // Cargar relaciones para respuesta
            $order->load([
                'user:id,name,email,phone_number',
                'pharmacy:id,name,address,phone',
                'details.drug:id,name,description'
            ]);

            return response()->json([
                'message' => 'Order updated successfully',
                'order' => $order
            ]);
        });
    }

    /**
     * Remove the specified order.
     * DELETE /api/orders/{id}
     */
    public function destroy(Orders $order)
    {
        return DB::transaction(function () use ($order) {
            // Solo se pueden eliminar órdenes en estado 'cotizacion'
            if ($order->status !== 'cotizacion') {
                return response()->json([
                    'error' => 'Only orders in quotation status can be deleted'
                ], 422);
            }

            $order->delete(); // Los detalles se eliminan automáticamente por CASCADE

            return response()->json([
                'message' => 'Order deleted successfully'
            ]);
        });
    }

    /**
     * Get orders by pharmacy.
     * GET /api/pharmacies/{pharmacy}/orders
     */
    public function getByPharmacy(Pharmacy $pharmacy, Request $request)
    {
        $status = $request->get('status');
        
        $orders = $pharmacy->orders()
            ->with([
                'user:id,name,email,phone_number',
                'details:id,order_id,drug_id,requested_amount,quantity_available,unit_price,products_total',
                'details.drug:id,name,description'
            ])
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('date', 'desc')
            ->paginate(15);

        return response()->json([
            'pharmacy' => $pharmacy->only(['id', 'name', 'address']),
            'orders' => $orders
        ]);
    }

    /**
     * Get orders by user.
     * GET /api/users/{user}/orders
     */
    public function getByUser(User $user, Request $request)
    {
        $status = $request->get('status');
        
        $orders = $user->orders()
            ->with([
                'pharmacy:id,name,address,phone',
                'details:id,order_id,drug_id,requested_amount,quantity_available,unit_price,products_total',
                'details.drug:id,name,description'
            ])
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('date', 'desc')
            ->paginate(15);

        return response()->json([
            'user' => $user->only(['id', 'name', 'email']),
            'orders' => $orders
        ]);
    }

    /**
     * Generate unique consecutive number for order.
     */
    private function generateConsecutive()
    {
        $prefix = 'ORD-' . date('Y') . '-';
        $lastOrder = Orders::where('consecutive', 'like', $prefix . '%')
            ->orderBy('consecutive', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->consecutive, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
