<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    public const METHODS = ['Cash', 'MoMo', 'Bank Transfer', 'Card', 'Other'];

    protected $fillable = [
        'sale_id',
        'amount',
        'method',
        'cash_account_id',
        'paid_at',
        'reference',
        'notes',
        'recorded_by',
        'is_backfilled',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
        'is_backfilled' => 'boolean',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class);
    }
}
