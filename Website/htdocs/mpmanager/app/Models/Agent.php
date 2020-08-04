<?php

namespace App\Models;

use App\Models\Address\Address;
use App\Models\Address\HasAddressesInterface;
use App\Models\Person\Person;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Inensus\Ticket\Models\Ticket;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class Agent
 * @package App\Models
 *
 * @property int $id
 * @property int $person_id
 * @property int $mini_grid_id
 * @property int $agent_commission_id
 * @property string $device_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $fire_base_token
 * @property double $balance
 * @property double $available_balance
 * @property string $remember_token
 */
class Agent extends Authenticatable implements JWTSubject
{
    public function setPasswordAttribute($password): void
    {
        $this->attributes['password'] = Hash::make($password);
    }

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'device_id'
    ];


    public function miniGrid()
    {
        return $this->hasOne(MiniGrid::Class, 'id', 'mini_grid_id');
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function balanceHistory()
    {
        return $this->hasMany(AgentBalanceHistory::class);
    }

    public function assignedAppliance()
    {
        return $this->hasMany(AgentAssignedAppliances::class);
    }

    public function addressDetails()
    {
        return $this->addresses()->with('city');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id', 'creator_id');
    }

    public function commission()
    {
        return $this->belongsTo(AgentCommission::class, 'agent_commission_id', 'id');
    }

    public function addresses(): HasOneOrMany
    {
        return $this->morphMany(Address::class, 'owner');
    }
}
