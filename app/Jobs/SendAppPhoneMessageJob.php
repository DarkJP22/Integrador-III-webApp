<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client as TwilioClient;

class SendAppPhoneMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $to;
    protected $message;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message, $to)
    {
        $this->to = $to;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle():void
    {

            try {
                $client = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));



                $response = $client->messages->create(
                // the number you'd like to send the message to
                    $this->to,
                    [
                // A Twilio phone number you purchased at twilio.com/console
                        'from' => config('services.twilio.from'), //env('TWILIO_FROM'),
                // the body of the text message you'd like to send
                        'body' => $this->message
                    ]
                );

                //\Log::info($response);
                

            } catch (\Twilio\Exceptions\RestException $e) {
               
                \Log::error($e->getMessage());

            } catch (ConfigurationException $e) {
                \Log::error($e->getMessage());
            }
    }
}
