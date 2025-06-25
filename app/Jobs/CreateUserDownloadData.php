<?php

namespace App\Jobs;


use App\Interfaces\GenerateDataDownload;
use App\Notifications\AccountCanceled;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class CreateUserDownloadData implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public GenerateDataDownload $generateData, public bool $sendNotification = true)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->generateData->generate();

        if ($this->sendNotification) {

            $this->generateData->user->notify(new AccountCanceled(URL::temporarySignedRoute(
                'user.download-data.show', now()->addDays(30), ['user' => $this->generateData->user->id]
            )));

        }
    }
}
