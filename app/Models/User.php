<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
    * Mass assignable attributes.
    */
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

    /**
    * Hidden attributes for arrays.
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
    * Cast attributes to specific types.
    */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at'    => 'datetime',
        'signed_at'         => 'datetime',
    ];

    /**
    * ===  ===  ===  ===  ===  ===  ===  =
    * RELATIONSHIPS
    * ===  ===  ===  ===  ===  ===  ===  =
    */

    public function branch(): BelongsTo {
        return $this->belongsTo( Branch::class );
    }

    public function permissions(): BelongsToMany {
        return $this->belongsToMany( Permission::class, 'user_permissions' )
        ->withPivot( 'selected_branches', 'selected_products', 'selected_catalogues' )
        ->withTimestamps();
    }

    public function kyc(): HasMany {
        return $this->hasMany( KYCData::class );
    }

    public function commissions(): HasMany {
        return $this->hasMany( Commission::class );
    }

    public function sales(): HasMany {
        return $this->hasMany( Sale::class );
    }

    public function conversations(): BelongsToMany {
        return $this->belongsToMany( Conversation::class, 'conversation_participants' );
    }

    public function sentMessages(): HasMany {
        return $this->hasMany( Message::class, 'sender_id' );
    }

    public function receivedMessages(): HasMany {
        return $this->hasMany( Message::class, 'receiver_id' );
    }

    /**
    * ===  ===  ===  ===  ===  ===  ===  =
    * PERMISSION HELPERS
    * ===  ===  ===  ===  ===  ===  ===  =
    */

    public function selectedBranches() {
        return $this->getSelectedItemsByPermission( 'manage_branch', Branch::class, 'selected_branches' );
    }

    public function selectedProducts() {
        return $this->getSelectedItemsByPermission( 'manage_products', Products::class, 'selected_products' );
    }

    public function selectedCatalogues() {
        return $this->getSelectedItemsByPermission( 'manage_catalogues', Catalogue::class, 'selected_catalogues' );
    }

    protected function getSelectedItemsByPermission( string $slug, string $modelClass, string $pivotField ) {
        $permission = $this->permissions()->where( 'slug', $slug )->first();

        if ( $permission && $permission->pivot?->$pivotField ) {
            $ids = json_decode( $permission->pivot->$pivotField, true );
            return $modelClass::whereIn( 'id', $ids )->get();
        }

        return collect();
    }

    public function hasPermission( string $permission ): bool {
        return $this->permissions->contains( 'slug', $permission );
    }

    /**
    * ===  ===  ===  ===  ===  ===  ===  =
    * SCOPES & ACCESSORS
    * ===  ===  ===  ===  ===  ===  ===  =
    */

    public function isActive(): bool {
        return $this->status === 1;
    }

    public function fullName(): string {
        return $this->name;
    }

    public function scopeActive( $query ) {
        return $query->where( 'status', 1 );
    }

    /**
    * Summary of hasCompletedKYC
    * @return bool
    */

    public function hasCompletedKYC(): bool {
        $requiredDocs = array_keys( kyc_docs() );

        // Get only approved KYC documents
        $approvedDocs = $this->kyc()
        ->where( 'status', 'approved' )
        ->pluck( 'doc_type' )
        ->toArray();

        return empty( array_diff( $requiredDocs, $approvedDocs ) );
    }

    public function missingKYC(): array {
        $requiredDocs = array_keys( kyc_docs() );

        $approvedDocs = $this->kyc()
        ->where( 'status', 'approved' )
        ->pluck( 'doc_type' )
        ->toArray();

        return array_diff( $requiredDocs, $approvedDocs );
    }
}
