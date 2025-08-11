<?php

namespace Tests\Unit;

use App\Events\PharmacyOrderUpdate;
use App\Orders;
use App\User;
use Illuminate\Broadcasting\PrivateChannel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PharmacyOrderUpdateEventTest extends TestCase
{

    #[Test]
    public function it_can_be_created_with_transmitter_and_order()
    {
        // Arrange
        $transmitter = new User([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        
        $order = new Orders([
            'id' => 1,
            'user_id' => 2,
            'pharmacy_id' => 1,
            'status' => 'cotizacion'
        ]);

        // Act
        $event = new PharmacyOrderUpdate($transmitter, $order);

        // Assert
        $this->assertInstanceOf(PharmacyOrderUpdate::class, $event);
        $this->assertEquals($transmitter->id, $event->transmitter->id);
        $this->assertEquals($order->id, $event->order->id);
    }

    #[Test]
    public function it_broadcasts_on_correct_private_channel()
    {
        // Arrange
        $transmitter = new User(['id' => 1, 'name' => 'Test']);
        $order = new Orders(['id' => 10, 'user_id' => 5]);

        // Act
        $event = new PharmacyOrderUpdate($transmitter, $order);
        $channels = $event->broadcastOn();

        // Assert
        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        
        $expectedChannelName = 'private-order.' . $order->id . '.' . $order->user_id;
        $this->assertEquals($expectedChannelName, $channels[0]->name);
    }

    #[Test]
    public function it_implements_should_broadcast_interface()
    {
        // Arrange
        $transmitter = new User(['id' => 1, 'name' => 'Test']);
        $order = new Orders(['id' => 1, 'user_id' => 1]);

        // Act
        $event = new PharmacyOrderUpdate($transmitter, $order);

        // Assert
        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, $event);
    }
}
