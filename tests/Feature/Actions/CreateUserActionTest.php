<?php


use App\Actions\CreateUser;
use App\Role;
use App\User;

it('create user with role of patient action', function () {
 
    $data = [
        'ide' => '503600224',
        'name' => 'Alonso Chavarria',
        'phone_number' => '89679098',
        'phone_country_code' => '+506',
        'password' => '123456'
    ];

    $user = (new CreateUser())($data, role('paciente'));
       
  
    $this->assertDatabaseHas(User::class,[
        'ide' => '503600224',
        'name' => 'ALONSO CHAVARRIA',
    ]);
    expect($user->ide)->toEqual('503600224');
    expect($user->roles[0])->name->toEqual('paciente');

});

