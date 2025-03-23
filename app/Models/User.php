<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'branch_id',
        'user_name',
        'name',
        'passport',
        'gender',
        'phone',
        'id_number',
        'commission_rate',
        'email',
        'status',
        'otp',
        'otp_expires_at',
        'signature',
        'signed_at',
        'password',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    /**
     * Get sales made by the user.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the conversations where the user is a participant.
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants', 'user_id', 'conversation_id');
    }

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the messages received by the user.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get the conversations the user participates in.
     */
    public function participatedConversations(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class, 'user_id');
    }

    /**
     * Get the kyc associated with the user.
     *
     * @return HasMany
     */
    public function kyc(): HasMany
    {
        return $this->hasMany(KYCData::class);
    }

    public function kycData()
    {
        return $this->hasMany(KYCData::class, 'user_id');
    }

    public function hasCompletedKYC()
    {
        $requiredDocs = array_keys(kyc_docs());

        // Fetch user's uploaded KYC documents with approval status
        $uploadedDocs = $this->kycData()
            ->whereIn('doc_type', $requiredDocs)
            ->where('status', 'approved')
            ->pluck('doc_type')
            ->toArray();

        // Check if all required docs exist and are approved
        return empty(array_diff($requiredDocs, $uploadedDocs));
    }
}
