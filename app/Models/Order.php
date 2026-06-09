<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_number',
        'customer_id',
        'staff_id',
        'total_price',
        'weight',
        'status',
        'payment_status',
        'payment_method',
        'pickup_address',
        'delivery_address',
        'pickup_time',
        'delivery_time',
        'special_instructions',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'pickup_time' => 'datetime',
            'delivery_time' => 'datetime',
        ];
    }

    /**
     * Relationships.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function deliveryAssignment(): HasOne
    {
        return $this->hasOne(DeliveryAssignment::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
