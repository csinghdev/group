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

    protected $ios_tokens, $android_tokens, $message, $type, $msg;

    /**
     * Create a new job instance.
     *
     * @param $ios_tokens
     * @param $android_tokens
     * @param $message
     * @param $type
     */
    public function __construct($ios_tokens, $android_tokens, $message, $type)
    {
        $this->ios_tokens = $ios_tokens;
        $this->android_tokens = $android_tokens;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->type === 'post')
        {
            $this->msg = PushNotification::Message($this->message['group'],array(
                'user' => $this->message['user'],
                'title' => $this->message['title']
            ));
        }

        if(count($this->ios_tokens) > 0)
        {
            $ios_device_tokens = array();
            foreach($this->ios_tokens as $ios_token)
            {
                array_push($ios_device_tokens, PushNotification::Device($ios_token));
                $ios_devices = PushNotification::DeviceCollection($ios_device_tokens);
                $collection1 = PushNotification::app('appNameIOS')
                    ->to($ios_devices)
                    ->send($this->msg);
            }
        }

        if(count($this->android_tokens) > 0)
        {
            $android_device_tokens = array();
            foreach($this->android_tokens as $android_token)
            {
                array_push($android_device_tokens, PushNotification::Device($android_token));
            }
            $android_devices = PushNotification::DeviceCollection($android_device_tokens);
            $collection2 = PushNotification::app('appNameAndroid')
                ->to($android_devices)
                ->send($this->msg);
        }


        // get response for each device push
//        foreach ($collection->pushManager as $push) {
//            $response = $push->getAdapter()->getResponse();
//        }

    }
}
