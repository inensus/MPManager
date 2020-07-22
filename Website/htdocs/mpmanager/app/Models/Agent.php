<?php

namespace App\Models;

use App\Models\Address\Address;
use App\Models\Person\Person;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

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


    public function address()
    {
        return $this->morphOne(Address::class, 'owner');
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
        return $this->address()->with('city');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
