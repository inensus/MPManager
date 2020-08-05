<?php

namespace App\Models\Person;

use App\Events\PersonDeleting;
use App\Events\PersonEvent;
use App\Models\Address\Address;
use App\Models\Address\HasAddressesInterface;
use App\Models\Agent;
use App\Models\AgentSoldAppliance;
use App\Models\BaseModel;
use App\Models\Country;
use App\Models\CustomerGroup;
use App\Models\Meter\MeterParameter;
use App\Models\PaymentHistory;
use App\Models\Role\RoleInterface;
use App\Models\Role\Roles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inensus\Ticket\Models\Ticket;

/**
 * Class Person
 *
 * @package App
 * @property int $id
 * @property string $title
 * @property string $education
 * @property string $name
 * @property string $surname
 * @property mixed $birth_date
 * @property string $sex TODO: replace with gender
 * @property int $nationality
 * @property int $is_customer
 *
 */
class Person extends BaseModel implements HasAddressesInterface, RoleInterface
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dispatchesEvents = [
        'deleting' => PersonDeleting::class,
    ];


    public function tickets()
    {
        return $this->morphMany(Ticket::class, 'owner');
    }

    public function saveAddress(Address $address): void
    {
        $this->addresses()->save($address);
    }

    public function addresses(): HasOneOrMany
    {
        return $this->morphMany(Address::class, 'owner');
    }

    public function citizenship(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'nationality', 'id');
    }


    public function meters(): MorphMany
    {
        return $this->morphMany(MeterParameter::class, 'owner');
    }

    public function roleOwner(): HasOneOrMany
    {
        return $this->morphMany(Roles::class, 'role_owner');
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(PaymentHistory::class, 'payer');
    }
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }
   public function agent(){
        return $this->hasOne(Agent::Class);
   }
    public function agentSoldAppliance(){
        return $this->hasOne(AgentSoldAppliance::Class);
    }
    public function __toString()
    {
        return sprintf('%s %s', $this->name, $this->surname);

    }


}
