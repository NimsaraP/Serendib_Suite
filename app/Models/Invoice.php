<?php

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'client_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'total_amount',
        'amount_paid',
        'payment_status',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issue_date' => 'date:Y-m-d',
            'due_date' => 'date:Y-m-d',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    /**
     * Model boot lifecycle checks for business validation.
     */
    protected static function booted(): void
    {
        static::saving(function (Invoice $invoice) {
            if ((float) $invoice->amount_paid > (float) $invoice->total_amount) {
                throw new InvalidArgumentException('Amount paid cannot exceed total amount.');
            }

            if ($invoice->booking_id) {
                $booking = $invoice->relationLoaded('booking')
                    ? $invoice->booking
                    : Booking::find($invoice->booking_id);

                if ($booking && (int) $invoice->client_id !== (int) $booking->client_id) {
                    throw new InvalidArgumentException('Invoice client must match the booking client.');
                }
            }
        });
    }

    /**
     * Get the booking associated with the invoice.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Get the client to whom the invoice is billed.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Balance due accessor: total_amount - amount_paid.
     */
    protected function balanceDue(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $balance = max(0.00, (float) $this->total_amount - (float) $this->amount_paid);
                return number_format($balance, 2, '.', '');
            }
        );
    }

    /**
     * Scope a query to only include unpaid invoices.
     */
    public function scopeUnpaid(Builder $query): void
    {
        $query->where('payment_status', 'Unpaid');
    }

    /**
     * Scope a query to only include partially paid invoices.
     */
    public function scopePartiallyPaid(Builder $query): void
    {
        $query->where('payment_status', 'Partially Paid');
    }

    /**
     * Scope a query to only include fully paid invoices.
     */
    public function scopePaid(Builder $query): void
    {
        $query->where('payment_status', 'Paid');
    }
}
