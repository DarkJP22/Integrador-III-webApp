<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterAuthorizationCode extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function generate()
    {
        return static::create([
             "code" => rand(0, 9999)
        ]);
    }
}
