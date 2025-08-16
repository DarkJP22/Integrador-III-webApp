<?php

namespace App\Events;

use App\Orders;
use App\OrderDetail;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PharmacyOrderUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $transmitter, public Orders $order)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.User.'.$this->order->user_id)
            //new PrivateChannel('order.'.$this->order->id.'.'.$this->order->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        // Cargar las relaciones necesarias
        $this->order->load([
            'pharmacy:id,name,address,phone',
            'user:id,name,email,phone_number',
            'details:id,order_id,drug_id,requested_amount,quantity_available,unit_price,products_total,description',
            'details.drug:id,name,description'
        ]);

        return [
            'transmitter' => [
                'id' => $this->transmitter->id,
                'name' => $this->transmitter->name,
                'email' => $this->transmitter->email,
            ],
            'order' => [
                'id' => $this->order->id,
                'consecutive' => $this->order->consecutive,
                'user_id' => $this->order->user_id,
                'pharmacy_id' => $this->order->pharmacy_id,
                'status' => $this->order->status,
                'order_total' => $this->order->order_total,
                'shipping_total' => $this->order->shipping_total,
                'shipping_cost' => $this->order->shipping_cost ?? 0,
                'payment_method' => $this->order->payment_method,
                'requires_shipping' => $this->order->requires_shipping,
                'address' => $this->order->address,
                'date' => $this->order->date,
                'created_at' => $this->order->created_at,
                'updated_at' => $this->order->updated_at,
                'pharmacy' => $this->order->pharmacy,
                'user' => $this->order->user,
            ],
            'orderDetails' => $this->order->details->map(function($detail) {
                return [
                    'id' => $detail->id,
                    'order_id' => $detail->order_id,
                    'drug_id' => $detail->drug_id,
                    'requested_amount' => $detail->requested_amount,
                    'quantity_available' => $detail->quantity_available,
                    'unit_price' => $detail->unit_price,
                    'products_total' => $detail->products_total,
                    'description' => $detail->description,
                    'drug' => $detail->drug,
                ];
            })->toArray(),
        ];
    }

}