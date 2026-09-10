<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Order $order)
    {
    }

    public function handle(): void
    {
        // Simulate sending mail — log the confirmation
        Log::info('SendOrderConfirmation: order #' . $this->order->id . ' for customer ' . $this->order->customer->email);
    }
}
