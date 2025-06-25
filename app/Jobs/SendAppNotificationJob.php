<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $message;
    protected $usersOnesignalIds;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title, $message = '', $usersOnesignalIds = [],  $data = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->usersOnesignalIds = $usersOnesignalIds;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $content = $this->message ?? $this->title;
        $title = $this->title;

        $noti = [
            'app_id' => config('services.onesignal.app_id'),
            'contents' => [
                'en' => $content,
                'es' => $content
            ],
            'headings' => [
                'en' => $title,
                'es' => $title
            ],
           
        ];

        if(count($this->usersOnesignalIds)){
            $noti['include_player_ids'] = $this->usersOnesignalIds;
        }else{
            $noti['included_segments'] = ["Subscribed Users"];
            
        }
        
        if(count($this->data)){
            $noti['data'] = $this->data;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic '. config('services.onesignal.api_key'),
            ])->post(config('services.onesignal.url_api'), $noti);
    
            if($response->failed()){
                 \Log::error('Mensaje Push OneSignal Http: ' . json_encode($response->json()));
                 //\Log::info('include_player_ids: ' . json_encode($this->usersOnesignalIds));
            }
           
            return $response;

        } catch (\Exception $ex) {
            \Log::error('ERROR ONESIGNAL: '. json_encode($ex->getMessage()));
        }
        
    }
}
