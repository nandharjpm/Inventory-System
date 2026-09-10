<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Order $order)
    {
    }

    public function handle(): void
    {
        try {
            // Queue the mailable so it is sent in the background by the queue worker
            Mail::to($this->order->customer->email)
                ->queue(new OrderConfirmationMail($this->order->load(['customer', 'items.product'])));

            Log::info('SendOrderConfirmation: mailable queued for order #' . $this->order->id);
        } catch (\Exception $ex) {
            Log::error('SendOrderConfirmation failed for order #' . $this->order->id . ': ' . $ex->getMessage());
        }
    }
}
