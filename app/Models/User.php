<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model - Represents a user in the Hero Seguros system.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \DateTime|null $email_verified_at
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 *
 * @property-read Collection<int, Quotation> $quotations
 * @property-read int|null $quotations_count
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all quotations belonging to this user.
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    /**
     * Get active quotations for this user (pending or approved and not expired).
     */
    public function activeQuotations(): HasMany
    {
        return $this->quotations()
            ->whereIn('status', ['pending', 'approved'])
            ->where('end_date', '>=', now());
    }

    /**
     * Get approved quotations for this user.
     */
    public function approvedQuotations(): HasMany
    {
        return $this->quotations()
            ->where('status', 'approved');
    }
}
