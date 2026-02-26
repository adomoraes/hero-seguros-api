<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Destination Model - Represents a travel destination with associated risk factors.
 *
 * @property int $id
 * @property string $country
 * @property string $code Country code (e.g., BR, US, FR)
 * @property float $base_risk_factor Base risk multiplier (e.g., 1.0, 1.5)
 * @property string|null $description
 * @property bool $active Whether this destination accepts new quotations
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 *
 * @property-read Collection<int, RiskFactor> $riskFactors
 * @property-read Collection<int, Quotation> $quotations
 * @property-read int|null $risk_factors_count
 * @property-read float|null $total_risk_multiplier
 */
class Destination extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'country',
        'code',
        'base_risk_factor',
        'description',
        'active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'base_risk_factor' => 'float',
            'active' => 'boolean',
        ];
    }

    /**
     * Get all risk factors associated with this destination.
     */
    public function riskFactors(): HasMany
    {
        return $this->hasMany(RiskFactor::class);
    }

    /**
     * Get all quotations for this destination.
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    /**
     * Scope to get only active destinations.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope to search by country name or code.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('country', 'like', "%{$term}%")
            ->orWhere('code', 'like', "%{$term}%");
    }

    /**
     * Calculate the total risk multiplier for this destination.
     * This is the base risk factor multiplied by all active risk factors.
     */
    public function calculateTotalRiskMultiplier(): float
    {
        $riskMultiplier = $this->riskFactors
            ->sum(fn(RiskFactor $factor) => $factor->multiplier);

        return $this->base_risk_factor + $riskMultiplier;
    }
}
