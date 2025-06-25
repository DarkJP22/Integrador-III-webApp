<?php


use App\Office;
use App\Product;

it('get list of exams(products) for selected lab', function () {
    loginAsUserApi();

    $lab2 = Office::factory()->create(['type' => 3]);
    $lab1 = Office::factory()->create(['type' => 3]);

    Product::factory()->count(2)->create(['office_id' => $lab1->id, 'laboratory' => 1]);
    Product::factory()->count(3)->create(['office_id' => $lab2->id, 'laboratory' => 1]);
    $this->get('/api/lab/exams?office_id='.$lab1->id)
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJson([
            'data' => [
                ['office_id' => $lab1->id],
                ['office_id' => $lab1->id],
            ],
        ]);

});
