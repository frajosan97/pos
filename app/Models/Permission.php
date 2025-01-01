<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Relationship with the User model through the pivot table.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions', 'permission_id', 'user_id')
            ->withPivot('selected_branches', 'selected_products', 'selected_catalogues');
    }

    /**
     * Scope a query to only include permissions that have been assigned to users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssigned($query)
    {
        return $query->whereHas('users');
    }

    /**
     * Get all permissions that are linked to a specific branch.
     *
     * @param int $branchId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissionsByBranch(int $branchId)
    {
        return $this->whereHas('users', function ($query) use ($branchId) {
            $query->whereJsonContains('user_permissions.selected_branches', $branchId);
        })->get();
    }

    /**
     * Get all permissions that are linked to a specific product.
     *
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissionsByProduct(int $productId)
    {
        return $this->whereHas('users', function ($query) use ($productId) {
            $query->whereJsonContains('user_permissions.selected_products', $productId);
        })->get();
    }

    /**
     * Get all permissions that are linked to a specific catalogue.
     *
     * @param int $catalogueId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissionsByCatalogue(int $catalogueId)
    {
        return $this->whereHas('users', function ($query) use ($catalogueId) {
            $query->whereJsonContains('user_permissions.selected_catalogues', $catalogueId);
        })->get();
    }
}
