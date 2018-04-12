<?php

namespace App\Jobs\StripeWebhooks;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\StripeWebhooks\StripeWebhookCall;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;


use App\Mail\SubscriptionCreated;



class CustomerSubscriptionCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $webhookCall;
    public function __construct(StripeWebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        
        Mail::to('rameshkumar86@gmail.com')
            ->send(new SubscriptionCreated($this->webhookCall->payload));
    }
}
