<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The customer user instance.
     *
     * @var User
     */
    protected User $customer;

    /**
     * The order instance.
     *
     * @var Order
     */
    protected Order $order;

    /**
     * The type of status update event (e.g., 'ready_for_delivery', 'delivered').
     *
     * @var string
     */
    protected string $eventType;

    /**
     * Create a new job instance.
     *
     * @param User $customer
     * @param Order $order
     * @param string $eventType
     */
    public function __construct(User $customer, Order $order, string $eventType)
    {
        $this->customer = $customer;
        $this->order = $order;
        $this->eventType = $eventType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        NotificationService::sendWhatsAppNotification($this->customer, $this->order, $this->eventType);
    }
}
