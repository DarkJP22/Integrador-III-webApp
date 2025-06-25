<?php


use App\Office;
use App\QuoteOrder;
use App\Role;
use Illuminate\Http\UploadedFile;

it('store a quote order', function () {
    loginAsUserApi();
    Role::factory()->create(['name' => 'paciente']);

    $lab = Office::factory()->create(['type' => 3]);

    $file = UploadedFile::fake()->create('hola.jpg', 1024);
    $data = [
        'tipo_identificacion' => '01',
        'ide' => '503600224',
        'name' => 'Alonso Chavarria',
        'phone_number' => '12345678',
        'photos' => [$file], // 2MB Max
        'office_id' => $lab->id,
    ];
    $this->post('/api/lab/quotes', $data)
        ->assertCreated();

    expect(QuoteOrder::first())->not->toBeNull()
        ->and(QuoteOrder::first()->name)->toBe('Alonso Chavarria')
        ->and(QuoteOrder::first()->office_id)->toBe($lab->id);
});
