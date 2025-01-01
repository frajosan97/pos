<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'user_name',
        'name',
        'passport',
        'gender',
        'phone',
        'id_number',
        'email',
        'status',
        'otp',
        'otp_expires_at',
        'password',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at'    => 'datetime',
    ];

    /**
     * Relationship with the Branch model.
     *
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relationship with the Permission model through the pivot table.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id')
            ->withPivot('selected_branches', 'selected_products', 'selected_catalogues'); // Explicitly load pivot columns
    }

    /**
     * Get the selected branches for the user based on the 'manage_branch' permission.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function selectedBranches()
    {
        $permission = $this->permissions()->withPivot('selected_branches')->where('slug', 'manager_branch')->first();

        if ($permission && isset($permission->pivot->selected_branches)) {
            $branchIds = json_decode($permission->pivot->selected_branches, true);
            return Branch::whereIn('id', $branchIds)->get();
        }

        return collect();
    }

    /**
     * Get the selected products for the user based on the 'manage_products' permission.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function selectedProducts()
    {
        $permission = $this->permissions()->withPivot('selected_products')->where('slug', 'manager_product')->first();

        if ($permission && isset($permission->pivot->selected_products)) {
            $productIds = json_decode($permission->pivot->selected_products, true);
            return Products::whereIn('id', $productIds)->get();
        }

        return collect();
    }

    /**
     * Get the selected catalogues for the user based on the 'manage_catalogues' permission.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function selectedCatalogues()
    {
        $permission = $this->permissions()->withPivot('selected_catalogues')->where('slug', 'manager_catalogue')->first();

        if ($permission && isset($permission->pivot->selected_catalogues)) {
            $catalogueIds = json_decode($permission->pivot->selected_catalogues, true);
            return Catalogue::whereIn('id', $catalogueIds)->get();
        }

        return collect();
    }

    /**
     * Check if the user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains('slug', $permission);
    }

    /**
     * Check if the user is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 1;
    }

    /**
     * Get the full name of the user.
     *
     * @return string
     */
    public function fullName(): string
    {
        return $this->name;
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get the commissions associated with the user.
     *
     * @return HasMany
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }
}
