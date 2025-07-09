<?php

namespace Database\Seeders;

use App\User;
use App\Pharmacy;
use App\Orders;
use App\OrderDetail;
use App\Drug;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar una farmacia existente, si no existe, mostrar advertencia
        $pharmacy = Pharmacy::first();
        if (!$pharmacy) {
            $this->command->warn('⚠️  No hay farmacias en la base de datos. Ejecuta primero los seeders principales.');
            return;
        }

        // Buscar un usuario existente, si no existe, mostrar advertencia
        $user = User::first();
        if (!$user) {
            $this->command->warn('⚠️  No hay usuarios en la base de datos. Ejecuta primero los seeders principales.');
            return;
        }

        // Buscar medicamentos existentes o crear algunos de prueba
        $drugs = Drug::take(5)->get();
        if ($drugs->isEmpty()) {
            $drugNames = ['Paracetamol', 'Ibuprofeno', 'Amoxicilina', 'Omeprazol', 'Losartán'];
            foreach ($drugNames as $name) {
                Drug::create([
                    'name' => $name,
                    'description' => "Medicamento {$name}",
                    'presentation' => 'Tabletas',
                ]);
            }
            $drugs = Drug::take(5)->get();
        }

        // Estados válidos del sistema
        $validStatuses = [
            'cotizacion', 
            'esperando_confirmacion', 
            'confirmado', 
            'preparando',  
            'cancelado', 
            'despachado'
        ];

        // Crear 10 órdenes de prueba
        for ($i = 1; $i <= 10; $i++) {
            $requiresShipping = fake()->boolean(60); // 60% probabilidad de envío
            
            $order = Orders::create([
                'consecutive' => 'ORD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'pharmacy_id' => $pharmacy->id,
                'user_id' => $user->id,
                'date' => now()->subDays(rand(0, 30)),
                'status' => fake()->randomElement($validStatuses),
                'payment_method' => fake()->boolean(30), // 30% SINPE, 70% efectivo
                'requires_shipping' => $requiresShipping,
                'address' => $requiresShipping ? fake()->address : null,
                'lat' => $requiresShipping ? fake()->latitude(9.5, 10.5) : null, // Costa Rica coords
                'lot' => $requiresShipping ? fake()->longitude(-85, -83) : null, // Costa Rica coords
                'order_total' => 0, // Se calculará después
                'shipping_total' => 0, // Se calculará después
                'voucher' => null,
            ]);

            // Crear detalles de la orden (2-4 medicamentos por orden)
            $numDetails = rand(2, 4);
            $subtotal = 0;

            for ($j = 0; $j < $numDetails; $j++) {
                $drug = $drugs->random();
                $requestedAmount = rand(1, 10);
                $quantityAvailable = rand(0, $requestedAmount + 2);
                $unitPrice = fake()->randomFloat(2, 500, 5000); // Precios en colones
                
                // Aplicar lógica de negocio para el cálculo
                $quantityForCalculation = $requestedAmount > $quantityAvailable 
                    ? $quantityAvailable 
                    : $requestedAmount;
                
                $productTotal = $quantityForCalculation * $unitPrice;
                $subtotal += $productTotal;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'drug_id' => $drug->id,
                    'requested_amount' => $requestedAmount,
                    'quantity_available' => $quantityAvailable,
                    'unit_price' => $unitPrice,
                    'description' => "Medicamento {$drug->name} - {$requestedAmount} unidades solicitadas",
                    'products_total' => $productTotal,
                ]);
            }

            // Calcular totales de la orden
            $orderTotal = $subtotal * 1.13; // Agregar IVA 13%
            $shippingCost = $requiresShipping ? rand(1000, 3000) : 0;
            $shippingTotal = $orderTotal + $shippingCost;

            // Actualizar la orden con los totales calculados
            $order->update([
                'order_total' => $orderTotal,
                'shipping_total' => $shippingTotal,
            ]);
        }

        $this->command->info('✅ Se crearon 10 órdenes con sus detalles correctamente.');
    }   
}
