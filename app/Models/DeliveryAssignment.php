<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAssignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'delivery_agent_id',
        'status',
        'assigned_at',
        'picked_up_at',
        'delivered_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * Relationships.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_agent_id');
    }
}
