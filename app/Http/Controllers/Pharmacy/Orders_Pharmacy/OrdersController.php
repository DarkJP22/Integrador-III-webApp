<?php

namespace App\Http\Controllers\Pharmacy\Orders_Pharmacy;

use App\Http\Controllers\Controller;
use App\Notifications\NewOrderPharmacie;
use App\Orders;
use App\OrderDetail;
use App\Pharmacy;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\ShippingRequired;
use App\Events\PharmacyOrderUpdate;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class OrdersController extends Controller
{
    /**
     * Verificar que el usuario tenga acceso a la farmacia de la orden
     */
    private function validatePharmacyAccess(?Orders $order = null)
    {
        $pharmacy = Auth::user()->pharmacies->first();
        $pharmacyId = $pharmacy ? $pharmacy->id : null;

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

        // Marcar las notificaciones de nuevas órdenes como leídas
        if (Auth::user()) {
            DB::table('notifications')
                ->where('notifiable_id', Auth::id())
                ->where('notifiable_type', 'App\User')
                ->where('type', 'App\Notifications\NewOrderPharmacie')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        // Construir query con filtros
        $query = Orders::with(['user', 'pharmacy'])
            ->where('pharmacy_id', $pharmacyId);

        // Filtro de búsqueda general (nombre de usuario)
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->where('consecutive', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filtro por estado usando Enum
        if ($request->filled('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        // Filtro por método de pago usando Enum
        if ($request->filled('payment_method') && is_array($request->get('payment_method'))) {
            $paymentMethods = $request->get('payment_method');
            $query->whereIn('payment_method', $paymentMethods);
        }

        // Filtro por requerimiento de envío usando Enum
        if ($request->filled('requires_shipping') && is_array($request->get('requires_shipping'))) {
            $shippingRequirements = $request->get('requires_shipping');
            $query->whereIn('requires_shipping', $shippingRequirements);
        }

        // Obtener órdenes paginadas - más recientes primero (por ID descendente)
        $orders = $query->orderBy('id', 'desc')->paginate(10);

        // Formatear fechas para todas las órdenes
        $orders->getCollection()->transform(function ($order) {
            $order->formatted_date = $order->date ? Carbon::parse($order->date)->format('d/m/Y H:i') : 'N/A';
            return $order;
        });

        // Pasar los Enums a la vista para generar las opciones
        $statusOptions = OrderStatus::getOptions();
        $paymentMethodOptions = PaymentMethod::getOptions();
        $shippingRequiredOptions = ShippingRequired::getOptions();

        return view('pharmacy.requests-orders.index', compact(
            'orders',
            'statusOptions',
            'paymentMethodOptions',
            'shippingRequiredOptions'
        ));
    }

    //Funciones de create y store son unicamente para pruebas 
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pasar opciones de Enums para los selects
        $statusOptions = OrderStatus::getOptions();
        $paymentMethodOptions = PaymentMethod::getOptions();
        $shippingRequiredOptions = ShippingRequired::getOptions();

        return view('pharmacy.requests-orders.create', compact(
            'statusOptions',
            'paymentMethodOptions',
            'shippingRequiredOptions'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
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
            'shipping_total' => 'required|numeric',
            'shipping_cost' => 'nullable|numeric',
            'voucher' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Manejar subida de voucher/comprobante
        if ($request->hasFile('voucher')) {
            $file = $request->file('voucher');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/vouchers', $fileName);
            $validated['voucher'] = 'vouchers/' . $fileName;
        }

        $order = Orders::create($validated);


        // Crear detalles de la orden si existen
        if ($request->has('details')) {
            foreach ($request->input('details') as $detail) {
                if (!empty($detail['drug_id']) && !empty($detail['requested_amount'])) {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'drug_id' => $detail['drug_id'],
                        'requested_amount' => $detail['requested_amount'],
                        'quantity_available' => $detail['quantity_available'] ?? 0,
                        'unit_price' => $detail['unit_price'] ?? 0,
                        'iva_percentage' => $detail['iva_percentage'] ?? 0,
                        'products_total' => ($detail['quantity_available'] ?? 0) * ($detail['unit_price'] ?? 0),
                    ]);
                }
            }
        }

        // Notificar a todos los usuarios de la farmacia sobre la nueva orden
        $pharmacy = Pharmacy::with('users')->find($validated['pharmacy_id']);
        if ($pharmacy && $pharmacy->users->count() > 0) {
            foreach ($pharmacy->users as $pharmacyUser) {
                $pharmacyUser->notify(new NewOrderPharmacie($order));
            }
        }

        return redirect()->route('pharmacy.orders.create')->with('success', 'Orden creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orders $order)
    {
        $this->validatePharmacyAccess($order);

        // Cargar detalles de la orden y el medicamento relacionado
        $order->load('details.drug');

        // Pasar opciones de Enums para los selects
        $statusOptions = OrderStatus::getOptions();
        $paymentMethodOptions = PaymentMethod::getOptions();
        $shippingRequiredOptions = ShippingRequired::getOptions();

        return view('pharmacy.requests-orders.edit', compact(
            'order',
            'statusOptions',
            'paymentMethodOptions',
            'shippingRequiredOptions'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orders $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(OrderStatus::getOptions())),
            'shipping_cost' => 'nullable|numeric|min:0',
            'details' => 'array',
            'details.*.quantity_available' => 'required|numeric|min:0',
            'details.*.unit_price' => 'required|numeric|min:0',
            'details.*.iva_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $this->validatePharmacyAccess($order);

        DB::transaction(function () use ($request, $order, $validated) {
            $subtotalSinIva = 0;
            $totalIva = 0;

            // Actualizar detalles de la orden con cálculo individual de IVA
            foreach ($request->input('details', []) as $id => $detailData) {
                $detail = OrderDetail::find($id);
                if ($detail && $detail->order_id === $order->id) {
                    $cantidadDisponible = floatval($detailData['quantity_available']);
                    $cantidadSolicitada = $detail->requested_amount;
                    $precio = floatval($detailData['unit_price']);
                    $ivaPercentage = floatval($detailData['iva_percentage']);

                    $cantidadParaCalculo = min($cantidadSolicitada, $cantidadDisponible);
                    $subtotalProducto = $cantidadParaCalculo * $precio;
                    $ivaProducto = $subtotalProducto * ($ivaPercentage / 100);
                    $totalProductoConIva = $subtotalProducto + $ivaProducto;

                    $detail->update([
                        'quantity_available' => $cantidadDisponible,
                        'unit_price' => $precio,
                        'iva_percentage' => $ivaPercentage,
                        'products_total' => $totalProductoConIva,
                    ]);

                    $subtotalSinIva += $subtotalProducto;
                    $totalIva += $ivaProducto;
                }
            }

            // Calcular totales finales
            $productsTotal = $subtotalSinIva + $totalIva;
            
            // Preservar shipping_cost existente - no debe cambiar en update
            $shippingCost = $order->shipping_cost ?? 0;
            $finalTotal = $productsTotal + $shippingCost;

            // Actualizar la orden
            $order->update([
                'status' => $validated['status'],
                'products_subtotal' => $subtotalSinIva,
                'iva_total' => $totalIva,
                'products_total' => $productsTotal,
                'shipping_cost' => $shippingCost,
                'order_total' => $finalTotal,
                'shipping_total' => $finalTotal,
            ]);
        });

        // Disparar evento de actualización de orden
        PharmacyOrderUpdate::dispatch(Auth::user(), $order);

        // Notificar push al usuario si tiene token
        $this->sendPushNotification($order);

        return redirect()->route('pharmacy.orders.index')->with('success', 'Orden actualizada correctamente.');
    }

    /**
     * Enviar notificación push al usuario de la orden
     */
    private function sendPushNotification(Orders $order)
    {
        $user = $order->user;
        if (!$user || !$user->push_token) {
            return;
        }

        $statusText = is_object($order->status) && method_exists($order->status, 'label')
            ? $order->status->label()
            : (string) $order->status;

        // Cargar datos completos para enviar en la notificación
        $order->load('details.drug');
        $notificationData = [
            'type' => 'order-update',
            'order' => $order->toArray(),
            'orderDetails' => $order->details->toArray()
        ];

        $this->sendNotification(
            $user->push_token,
            'Estado de orden actualizado',
            "El estado de tu orden #{$order->consecutive} ha cambiado a {$statusText}.",
            $notificationData
        );
    }

    /**
     * Enviar notificación push a OneSignal
     */
    private function sendNotification($playerId, $title, $message, $data = null)
    {
        if (!$playerId) return;

        try {
            $payload = [
                'app_id' => env('ONESIGNAL_APP_ID'),
                'headings' => ['en' => $title],
                'contents' => ['en' => $message],
                'include_player_ids' => [$playerId],
                'target_channel' => "push"
            ];

            if ($data) {
                $payload['data'] = $data;
            }

            Http::withHeaders([
                'Authorization' => 'Basic ' . config('services.onesignal.api_key'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(config('services.onesignal.url_api', 'https://onesignal.com/api/v1/notifications'), $payload);

        } catch (Exception $e) {
            Log::error("Error enviando notificación OneSignal: " . $e->getMessage());
        }
    }

    /**
     * Responder a una cotización (cambiar de 'cotizacion' a 'esperando_confirmacion')
     */
    public function respondQuote(Request $request, Orders $order)
    {
        Log::info('Datos recibidos en respondQuote:', [
            'all' => $request->all(),
        ]);
        $this->validatePharmacyAccess($order);

        if ($order->status !== OrderStatus::COTIZACION) {
            return redirect()->back()->with('error', 'Solo se pueden responder órdenes en estado de cotización.');
        }

        $validated = $request->validate([
            'details' => 'array',
            'details.*.quantity_available' => 'required|numeric|min:0',
            'details.*.unit_price' => 'required|numeric|min:0',
            'details.*.iva_percentage' => 'required|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $order) {
            $subtotalSinIva = 0;
            $totalIva = 0;

            // Actualizar detalles de la orden con cálculo individual de IVA
            foreach ($request->input('details', []) as $id => $detailData) {
                $detail = OrderDetail::find($id);
                if ($detail && $detail->order_id === $order->id) {
                    $cantidadDisponible = floatval($detailData['quantity_available']);
                    $cantidadSolicitada = $detail->requested_amount;
                    $precio = floatval($detailData['unit_price']);
                    $ivaPercentage = floatval($detailData['iva_percentage']);

                    $cantidadParaCalculo = min($cantidadSolicitada, $cantidadDisponible);
                    $subtotalProducto = $cantidadParaCalculo * $precio;
                    $ivaProducto = $subtotalProducto * ($ivaPercentage / 100);
                    $totalProductoConIva = $subtotalProducto + $ivaProducto;

                    $detail->update([
                        'quantity_available' => $cantidadDisponible,
                        'unit_price' => $precio,
                        'iva_percentage' => $ivaPercentage,
                        'products_total' => $totalProductoConIva // Guardamos el total con IVA
                    ]);

                    $subtotalSinIva += $subtotalProducto;
                    $totalIva += $ivaProducto;
                }
            }

            // Calcular totales finales
            $productsTotal = $subtotalSinIva + $totalIva;

            // Actualizar la orden y cambiar estado automáticamente
            $order->update([
                'status' => OrderStatus::ESPERANDO_CONFIRMACION,
                'products_subtotal' => $subtotalSinIva,     // Subtotal sin IVA
                'iva_total' => $totalIva,                   // Total de IVA
                'products_total' => $productsTotal,         // Total productos con IVA
                'shipping_cost' => 0,                       // Sin shipping aún
                'order_total' => $productsTotal,            // Solo productos por ahora
                'shipping_total' => $productsTotal,         // Sin shipping aún
            ]);
        });

        // Disparar evento de actualización de orden
        PharmacyOrderUpdate::dispatch(Auth::user(), $order);

        return redirect()->route('pharmacy.orders.index')->with('success', 'Cotización enviada exitosamente.');
    }

    /**
     * Confirmar método de pago y cambiar a estado 'preparando'
     */
    public function confirmPayment(Request $request, Orders $order)
    {
        $this->validatePharmacyAccess($order);

        if ($order->status !== OrderStatus::CONFIRMADO) {
            return redirect()->back()->with('error', 'Solo se puede confirmar el pago de órdenes confirmadas.');
        }

        // Si es SINPE, verificar que el voucher esté subido
        if ($order->payment_method == PaymentMethod::SINPE && !$order->voucher) {
            return redirect()->back()->with('error', 'No se ha subido el comprobante SINPE.');
        }

        // Validar shipping_cost si la orden requiere envío
        $validated = $request->validate([
            'shipping_cost' => $order->requiresShipping() ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
        ]);

        try {
            // Calcular shipping_cost solo si la orden requiere envío
            $shippingCost = 0;
            if ($order->requiresShipping()) {
                $shippingCost = floatval($validated['shipping_cost']);
            }

            // Recalcular totales con el shipping_cost
            $productsTotal = $order->products_total ?? 0;
            $finalTotal = $productsTotal + $shippingCost;

            $order->update([
                'status' => OrderStatus::PREPARANDO,
                'shipping_cost' => $shippingCost,
                'order_total' => $finalTotal,
                'shipping_total' => $finalTotal,
            ]);

            // Disparar evento de actualización de orden
            PharmacyOrderUpdate::dispatch(Auth::user(), $order);

            return redirect()->route('pharmacy.orders.edit', $order)->with('success', 'Pago confirmado. La orden está ahora en preparación.');
        } catch (\Exception $e) {
            Log::error('Error confirming payment:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al confirmar el pago. Intente nuevamente.');
        }
    }

    /**
     * Marcar orden como despachada
     */
    public function markAsDispatched(Orders $order)
    {
        $this->validatePharmacyAccess($order);

        if ($order->status !== OrderStatus::PREPARANDO) {
            return redirect()->back()->with('error', 'Solo se pueden despachar órdenes que estén en preparación.');
        }

        try {
            $order->update(['status' => OrderStatus::DESPACHADO]);

            // Disparar evento de actualización de orden
            PharmacyOrderUpdate::dispatch(Auth::user(), $order);

            return redirect()->route('pharmacy.orders.edit', $order)->with('success', 'Orden marcada como despachada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al marcar como despachada. Intente nuevamente.');
        }
    }

    /**
     * Mostrar el voucher de la orden de forma segura
     */
    public function showVoucher(Orders $order)
    {
        $this->validatePharmacyAccess($order);

        if (!$order->voucher) {
            abort(404, 'No se encontró el voucher para esta orden.');
        }

        // Si es una imagen en base64
        if (strpos($order->voucher, 'data:image/') === 0) {
            // Extraer el tipo de imagen y los datos base64
            preg_match('/data:image\/([^;]+);base64,(.*)/', $order->voucher, $matches);
            
            if (count($matches) !== 3) {
                abort(404, 'Formato de voucher inválido.');
            }
            
            $imageType = $matches[1];
            $base64Data = $matches[2];
            $imageData = base64_decode($base64Data);
            
            if ($imageData === false) {
                abort(404, 'Error al decodificar el voucher.');
            }
            
            $mimeType = 'image/' . $imageType;
            
            return response($imageData)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="voucher_' . $order->consecutive . '.' . $imageType . '"');
        }
        
        // Si es una ruta de archivo (comportamiento anterior)
        $path = storage_path('app/public/' . $order->voucher);
        
        if (!file_exists($path)) {
            abort(404, 'El archivo del voucher no existe.');
        }

        $mimeType = mime_content_type($path);
        
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="voucher_' . $order->consecutive . '"'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orders $order)
    {
        $this->validatePharmacyAccess($order);
        $order->delete();
        return redirect()->route('pharmacy.orders.index')->with('success', 'Orden eliminada.');
    }
}
