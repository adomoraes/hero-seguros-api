<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Quotation Model - Represents an insurance quotation request.
 *
 * @property int $id
 * @property int $user_id Foreign key to users table
 * @property int $destination_id Foreign key to destinations table
 * @property int $plan_id Foreign key to plans table
 * @property \DateTime $start_date Trip start date
 * @property \DateTime $end_date Trip end date
 * @property int $travelers Number of travelers
 * @property float|null $premium Calculated premium in BRL
 * @property string $status Quotation status (pending, approved, rejected, expired)
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 *
 * @property-read User $user
 * @property-read Destination $destination
 * @property-read Plan $plan
 */
class Quotation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'destination_id',
        'plan_id',
        'start_date',
        'end_date',
        'travelers',
        'premium',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'travelers' => 'integer',
            'premium' => 'float',
            'status' => 'string',
        ];
    }

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_EXPIRED = 'expired';

    /**
     * Get all available statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_EXPIRED,
        ];
    }

    /**
     * Get the user who created this quotation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the destination for this quotation.
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * Get the plan for this quotation.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Scope to get only pending quotations.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get only approved quotations.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope to get only active quotations (not expired and not rejected).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED])
            ->where('end_date', '>=', now());
    }

    /**
     * Scope to get quotations within a date range.
     */
    public function scopeInDateRange(Builder $query, \DateTime $startDate, \DateTime $endDate): Builder
    {
        return $query->whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate]);
    }

    /**
     * Calculate the duration of the trip in days.
     */
    public function getDurationInDays(): int
    {
        return $this->end_date->diffInDays($this->start_date) + 1;
    }

    /**
     * Calculate the base premium before applying risk factors.
     * Formula: plan_daily_rate × duration × travelers
     */
    public function calculateBasePremium(): float
    {
        $duration = $this->getDurationInDays();

        return $this->plan->daily_rate * $duration * $this->travelers;
    }

    /**
     * Calculate the final premium with risk factors applied.
     * Formula: base_premium × total_risk_multiplier
     */
    public function calculateFinalPremium(): float
    {
        $basePremium = $this->calculateBasePremium();
        $riskMultiplier = $this->destination->calculateTotalRiskMultiplier();

        return $basePremium * $riskMultiplier;
    }

    /**
     * Check if this quotation is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED || $this->end_date < now();
    }

    /**
     * Approve this quotation and set the premium.
     */
    public function approve(): void
    {
        $this->status = self::STATUS_APPROVED;
        $this->premium = $this->calculateFinalPremium();
        $this->save();
    }

    /**
     * Reject this quotation.
     */
    public function reject(): void
    {
        $this->status = self::STATUS_REJECTED;
        $this->save();
    }
}
