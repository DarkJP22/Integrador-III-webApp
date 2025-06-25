<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use App\Office;
use App\Pharmacy;
use App\Role;
use App\User;

use function Pest\Laravel\actingAs;

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');

uses()
    ->beforeEach(fn () => Role::factory()->create())
    ->in('Feature');
/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function role(string $role = 'paciente')
{
    return Role::where('name', $role)->first() ?? Role::factory()->create(['name' => $role]);
}
function loginAsUser(?User $user = null, string $driver = 'web'): User
{
    $user = $user ?? User::factory()->create();
    
    actingAs($user, $driver);

    return $user;
}

function loginAsUserApi(?User $user = null): User
{
    return loginAsUser($user, 'api');
}
function loginAsUserPharmacy(?User $user = null, ?Pharmacy $pharmacy = null): User
{
    $user = $user ?? User::factory()->create();
    $pharmacy = $pharmacy ?? Pharmacy::factory()->create();
    $user->assignRole(role('farmacia'));
    $user->pharmacies()->attach($pharmacy);
  
    return loginAsUser($user);
}
function loginAsUserClinic(?User $user = null, ?Office $office = null): User
{
    $user = $user ?? User::factory()->create();
    $office = $office ?? Office::factory()->create();
    $user->assignRole(role('clinica'));
    $user->offices()->attach($office);
  
    return loginAsUser($user);
}
function loginAsUserLab(?User $user = null, ?Office $office = null): User
{
    $user = $user ?? User::factory()->create();
    $office = $office ?? Office::factory()->create();
    $user->assignRole(role('laboratorio'));
    $user->offices()->attach($office);
  
    return loginAsUser($user);
}

function loginAsUserBeautician(?User $user = null, ?Office $office = null): User
{
    $user = $user ?? User::factory()->create();
    $office = $office ?? Office::factory()->create();
    $user->assignRole(role('esteticista'));
    $user->offices()->attach($office);
  
    return loginAsUser($user);
}

function loginAsUserAssistant(?User $user = null, ?Office $office = null): User
{
    $user = $user ?? User::factory()->create();
    $office = $office ?? Office::factory()->create();
    $user->assignRole(role('asistente'));
    $user->clinicsAssistants()->attach($office);
  
    return loginAsUser($user);
}
function loginAsUserOperator(?User $user = null): User
{
    $user = $user ?? User::factory()->create();
    $user->assignRole(role('operador'));
  
    return loginAsUser($user);
}

function loginAsUserMedic(?User $user = null): User
{
    $user = $user ?? User::factory()->create();
    $user->assignRole(role('medico'));
  
    return loginAsUser($user);
}
