<?php
namespace App\Actions;

use App\DiscountHistory;
use App\User;


class CreateUserDiscount {


    public function create(User $user, array $data) : DiscountHistory
    {
        $data['created_by'] = auth()->user()->id;

        return $user->discountHistories()->create($data);

    }

   
}