<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class StaffAssignmentService
{
    /**
     * Determine and return the next active laundry staff user using a round-robin algorithm.
     *
     * @return \App\Models\User|null The selected User model instance, or null if no active staff exist.
     */
    public function assignNextStaff(): ?User
    {
        // 1. Retrieve all users where role = 'staff' AND is_active = 1, ordered by id ASC
        $staffMembers = User::where('role', 'staff')
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        if ($staffMembers->isEmpty()) {
            Log::warning('No active staff users available for round-robin assignment.');
            return null;
        }

        // 2. Find the staff member who was assigned the most recent order (MAX created_at among orders with staff_id not null)
        $lastAssignedOrder = Order::whereNotNull('staff_id')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc') // Tie-breaker for identical created_at timestamps
            ->first();

        // 3. If no previous assignment exists, assign to the first staff member
        if (!$lastAssignedOrder) {
            return $staffMembers->first();
        }

        $lastAssignedStaffId = $lastAssignedOrder->staff_id;

        // Find the index of the last assigned staff member in the active staff list
        $lastIndex = $staffMembers->search(function ($user) use ($lastAssignedStaffId) {
            return $user->id === $lastAssignedStaffId;
        });

        // 4. Pick the NEXT staff member in the list after that one (wrap around to first if at end)
        // If the last assigned staff member is no longer active (not found in the current list), we default to the first active staff.
        if ($lastIndex === false) {
            return $staffMembers->first();
        }

        $nextIndex = ($lastIndex + 1) % $staffMembers->count();

        // 5. Return the selected User model instance
        return $staffMembers->get($nextIndex);
    }
}
