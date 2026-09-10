<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(protected Order $order)
    {
    }

    public function build()
    {
        return $this->subject('Order Confirmation #' . $this->order->id)
            ->view('emails.order_confirmation')
            ->with(['order' => $this->order]);
    }
}
