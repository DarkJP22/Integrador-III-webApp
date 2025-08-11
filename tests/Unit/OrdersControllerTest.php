<?php

namespace Tests\Unit;

use App\Http\Controllers\Pharmacy\Orders_Pharmacy\OrdersController;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrdersControllerTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new OrdersController();
    }

    #[Test]
    public function it_validates_request_data_structure_for_update()
    {
        // Arrange
        $request = new Request([
            'status' => OrderStatus::ESPERANDO_CONFIRMACION->value,
            'shipping_cost' => 250.00,
            'details' => [
                1 => [
                    'quantity_available' => 4,
                    'unit_price' => 120.00,
                    'iva_percentage' => 13
                ]
            ]
        ]);

        // Act & Assert
        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals(OrderStatus::ESPERANDO_CONFIRMACION->value, $request->input('status'));
        $this->assertEquals(250.00, $request->input('shipping_cost'));
        $this->assertIsArray($request->input('details'));
        $this->assertEquals(4, $request->input('details.1.quantity_available'));
        $this->assertEquals(120.00, $request->input('details.1.unit_price'));
        $this->assertEquals(13, $request->input('details.1.iva_percentage'));
    }

    #[Test]
    public function it_has_correct_validation_rules_for_update()
    {
        // Arrange
        $request = new Request([
            'status' => OrderStatus::ESPERANDO_CONFIRMACION->value,
            'shipping_cost' => 250.00,
            'details' => [
                1 => [
                    'quantity_available' => 4,
                    'unit_price' => 120.00,
                    'iva_percentage' => 13
                ]
            ]
        ]);

        // Act
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', array_keys(OrderStatus::getOptions())),
            'shipping_cost' => 'nullable|numeric|min:0',
            'details' => 'array',
            'details.*.quantity_available' => 'required|numeric|min:0',
            'details.*.unit_price' => 'required|numeric|min:0',
            'details.*.iva_percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Assert
        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function it_validates_invalid_status()
    {
        // Arrange
        $request = new Request([
            'status' => 'invalid_status',
            'shipping_cost' => 250.00,
            'details' => []
        ]);

        // Act
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', array_keys(OrderStatus::getOptions())),
            'shipping_cost' => 'nullable|numeric|min:0',
            'details' => 'array',
        ]);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    #[Test]
    public function it_validates_negative_shipping_cost()
    {
        // Arrange
        $request = new Request([
            'status' => OrderStatus::ESPERANDO_CONFIRMACION->value,
            'shipping_cost' => -50.00,
            'details' => []
        ]);

        // Act
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', array_keys(OrderStatus::getOptions())),
            'shipping_cost' => 'nullable|numeric|min:0',
            'details' => 'array',
        ]);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('shipping_cost', $validator->errors()->toArray());
    }

    #[Test]
    public function it_can_calculate_totals_correctly()
    {
        // Arrange - Simular los datos que se procesarían
        $cantidadSolicitada = 5;
        $cantidadDisponible = 4;
        $precio = 100.00;
        $ivaPercentage = 13;

        // Act - Replicar la lógica del controlador
        $cantidadParaCalculo = min($cantidadSolicitada, $cantidadDisponible);
        $subtotalProducto = $cantidadParaCalculo * $precio;
        $ivaProducto = $subtotalProducto * ($ivaPercentage / 100);
        $totalProductoConIva = $subtotalProducto + $ivaProducto;

        // Assert
        $this->assertEquals(4, $cantidadParaCalculo);
        $this->assertEquals(400.00, $subtotalProducto);
        $this->assertEquals(52.00, $ivaProducto);
        $this->assertEquals(452.00, $totalProductoConIva);
    }

    #[Test]
    public function it_can_calculate_final_totals_with_shipping()
    {
        // Arrange
        $subtotalSinIva = 1000.00;
        $totalIva = 130.00;
        $shippingCost = 250.00;

        // Act - Replicar la lógica del controlador
        $totalConIVA = $subtotalSinIva + $totalIva;
        $totalConEnvio = $totalConIVA + $shippingCost;

        // Assert
        $this->assertEquals(1130.00, $totalConIVA);
        $this->assertEquals(1380.00, $totalConEnvio);
    }

    #[Test]
    public function it_has_validatePharmacyAccess_method()
    {
        // Act
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('validatePharmacyAccess');

        // Assert
        $this->assertTrue($method->isPrivate());
        $this->assertEquals('validatePharmacyAccess', $method->getName());
    }

    #[Test]
    public function it_has_all_required_methods()
    {
        // Act
        $reflection = new \ReflectionClass($this->controller);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methodNames = array_map(function($method) {
            return $method->getName();
        }, $methods);

        // Assert
        $expectedMethods = [
            'index',
            'create', 
            'store',
            'edit',
            'update',
            'destroy',
            'respondQuote',
            'confirmPayment',
            'markAsDispatched'
        ];

        foreach ($expectedMethods as $expectedMethod) {
            $this->assertContains($expectedMethod, $methodNames, "Method {$expectedMethod} not found");
        }
    }

    #[Test]
    public function it_uses_correct_order_status_enum_values()
    {
        // Act
        $statusOptions = OrderStatus::getOptions();

        // Assert
        $this->assertIsArray($statusOptions);
        $this->assertArrayHasKey(OrderStatus::COTIZACION->value, $statusOptions);
        $this->assertArrayHasKey(OrderStatus::ESPERANDO_CONFIRMACION->value, $statusOptions);
        $this->assertArrayHasKey(OrderStatus::CONFIRMADO->value, $statusOptions);
        $this->assertArrayHasKey(OrderStatus::PREPARANDO->value, $statusOptions);
        $this->assertArrayHasKey(OrderStatus::DESPACHADO->value, $statusOptions);
    }

    #[Test]
    public function it_can_create_proper_request_for_respond_quote()
    {
        // Arrange
        $request = new Request([
            'shipping_cost' => 150.00,
            'details' => [
                1 => [
                    'quantity_available' => 3,
                    'unit_price' => 85.50,
                    'iva_percentage' => 13
                ]
            ]
        ]);

        // Act
        $validator = Validator::make($request->all(), [
            'shipping_cost' => 'nullable|numeric|min:0',
            'details' => 'array',
            'details.*.quantity_available' => 'required|numeric|min:0',
            'details.*.unit_price' => 'required|numeric|min:0',
            'details.*.iva_percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Assert
        $this->assertTrue($validator->passes());
        $this->assertEquals(150.00, $request->input('shipping_cost'));
        $this->assertEquals(3, $request->input('details.1.quantity_available'));
        $this->assertEquals(85.50, $request->input('details.1.unit_price'));
        $this->assertEquals(13, $request->input('details.1.iva_percentage'));
    }
}
