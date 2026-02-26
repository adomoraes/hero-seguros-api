<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * RiskFactor Model - Represents risk factors that affect insurance premiums for destinations.
 *
 * @property int $id
 * @property int $destination_id Foreign key to destinations table
 * @property string $category Risk category (war, natural_disaster, disease, etc)
 * @property float $multiplier Risk multiplier (1.5, 2.0, etc)
 * @property string|null $description
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 *
 * @property-read Destination $destination
 */
class RiskFactor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'destination_id',
        'category',
        'multiplier',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'multiplier' => 'float',
        ];
    }

    /**
     * Risk category constants.
     */
    public const CATEGORY_WAR = 'war';
    public const CATEGORY_NATURAL_DISASTER = 'natural_disaster';
    public const CATEGORY_DISEASE = 'disease';
    public const CATEGORY_TERRORISM = 'terrorism';
    public const CATEGORY_CIVIL_UNREST = 'civil_unrest';
    public const CATEGORY_POLITICAL_INSTABILITY = 'political_instability';

    /**
     * Get all available risk categories.
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_WAR,
            self::CATEGORY_NATURAL_DISASTER,
            self::CATEGORY_DISEASE,
            self::CATEGORY_TERRORISM,
            self::CATEGORY_CIVIL_UNREST,
            self::CATEGORY_POLITICAL_INSTABILITY,
        ];
    }

    /**
     * Get the destination this risk factor belongs to.
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * Scope to get risk factors by category.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get high-risk factors (multiplier >= 1.5).
     */
    public function scopeHighRisk(Builder $query): Builder
    {
        return $query->where('multiplier', '>=', 1.5);
    }

    /**
     * Scope to get moderate-risk factors (1.0 <= multiplier < 1.5).
     */
    public function scopeModerateRisk(Builder $query): Builder
    {
        return $query->whereBetween('multiplier', [1.0, 1.4]);
    }

    /**
     * Scope to get low-risk factors (multiplier < 1.0).
     */
    public function scopeLowRisk(Builder $query): Builder
    {
        return $query->where('multiplier', '<', 1.0);
    }
}
