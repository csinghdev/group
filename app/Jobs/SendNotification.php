<?php

namespace App\Jobs;

use App\Jobs\Job;
use Davibennun\LaravelPushNotification\Facades\PushNotification as PushNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNotification extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $ios_tokens, $android_tokens, $message;

    /**
     * Create a new job instance.
     *
     * @param $ios_tokens
     * @param $android_tokens
     * @param $message
     */
    public function __construct($ios_tokens, $android_tokens, $message)
    {
        $this->ios_tokens = $ios_tokens;
        $this->android_tokens = $android_tokens;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $msg = PushNotification::Message($this->message['group'],array(
            'user' => $this->message['user'],
            'title' => $this->message['title']
        ));

        $ios_device_tokens = array();
        $android_device_tokens = array();

        foreach($this->ios_tokens as $ios_token)
        {
            array_push($ios_device_tokens, PushNotification::Device($ios_token));
        }

        foreach($this->android_tokens as $android_token)
        {
            array_push($android_device_tokens, PushNotification::Device($android_token));
        }

        $ios_devices = PushNotification::DeviceCollection($ios_device_tokens);
        $android_devices = PushNotification::DeviceCollection($ios_device_tokens);

        $collection1 = PushNotification::app('appNameIOS')
            ->to($ios_devices)
            ->send($msg);

        $collection2 = PushNotification::app('appNameAndroid')
            ->to($android_devices)
            ->send($msg);
        
        // get response for each device push
//        foreach ($collection->pushManager as $push) {
//            $response = $push->getAdapter()->getResponse();
//        }

    }
}
