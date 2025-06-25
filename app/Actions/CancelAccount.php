<?php

namespace App\Actions;

use App\Jobs\CreateUserDownloadData;
use App\User;


class CancelAccount
{

    /**
     * @throws \Exception
     */
    public function execute(User $user): void
    {
        $user->active = 0;
        $user->cancel_at = now();
        $user->save();

        $dataToGenerate = app(PrepareDataForUser::class)->handle($user);

        CreateUserDownloadData::dispatch($dataToGenerate);
    }

}