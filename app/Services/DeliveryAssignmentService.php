<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DeliveryAssignmentService
{
    /**
     * Assign the next available delivery agent using round-robin.
     *
     * @return \App\Models\User|null The assigned delivery agent User instance, or null if none available.
     */
    public function assignNextAgent(): ?User
    {
        $agents = User::where('role', 'delivery')
                      ->where('is_active', 1)
                      ->orderBy('id', 'asc')
                      ->get();

        if ($agents->isEmpty()) {
            Log::warning('LOMS: No active delivery agents available for assignment.');
            return null;
        }

        $lastAssigned = Order::whereNotNull('delivery_agent_id')
                             ->latest('updated_at')
                             ->value('delivery_agent_id');

        if (!$lastAssigned) {
            return $agents->first();
        }

        $currentIndex = $agents->search(fn($a) => $a->id === $lastAssigned);
        $nextIndex = ($currentIndex === false)
            ? 0
            : ($currentIndex + 1) % $agents->count();

        return $agents[$nextIndex];
    }
}
