<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Plan Model - Represents an insurance plan offered by Hero Seguros.
 *
 * @property int $id
 * @property string $name Plan name (e.g., Standard, Premium, Economy)
 * @property string $description
 * @property string $coverage_type Coverage level (basic, standard, premium)
 * @property float $daily_rate Daily rate in BRL
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 *
 * @property-read Collection<int, Quotation> $quotations
 * @property-read int|null $quotations_count
 */
class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'coverage_type',
        'daily_rate',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'coverage_type' => 'string',
            'daily_rate' => 'float',
        ];
    }

    /**
     * Coverage type constants.
     */
    public const COVERAGE_BASIC = 'basic';
    public const COVERAGE_STANDARD = 'standard';
    public const COVERAGE_PREMIUM = 'premium';

    /**
     * Get all available coverage types.
     */
    public static function getCoverageTypes(): array
    {
        return [
            self::COVERAGE_BASIC,
            self::COVERAGE_STANDARD,
            self::COVERAGE_PREMIUM,
        ];
    }

    /**
     * Get all quotations for this plan.
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    /**
     * Scope to get plans by coverage type.
     */
    public function scopeByCoverageType(Builder $query, string $coverage): Builder
    {
        return $query->where('coverage_type', $coverage);
    }

    /**
     * Scope to get plans within a price range.
     */
    public function scopeByPriceRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('daily_rate', [$min, $max]);
    }

    /**
     * Calculate the total cost for a given duration and number of travelers.
     */
    public function calculateCost(int $days, int $travelers = 1): float
    {
        return $this->daily_rate * $days * $travelers;
    }
}
