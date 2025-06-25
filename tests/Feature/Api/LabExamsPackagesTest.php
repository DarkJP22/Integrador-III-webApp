<?php


use App\ExamPackage;
use App\Office;

it('get list of exams packages for selected lab', function () {
    loginAsUserApi();

    $lab2 = Office::factory()->create(['type' => 3]);
    $lab1 = Office::factory()->create(['type' => 3]);

    ExamPackage::factory()->count(2)->create(['office_id' => $lab1->id]);
    ExamPackage::factory()->count(3)->create(['office_id' => $lab2->id]);

    $this->get('/api/lab/exams-packages?office_id='.$lab1->id)
        ->assertOk()
        ->assertJsonStructure(['links', 'data' => ['*' => ['office_id']]])
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.office_id', $lab1->id);

});
