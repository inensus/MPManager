<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 12.07.18
 * Time: 19:11
 */

namespace App\Http\Services;

use App\Models\Address\Address;
use App\Models\Address\HasAddressesInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AddressService
{
    public function __construct(private Address $address)
    {
    }

    // fills the object and returns it without saving.
    public function instantiate(array $params): Address
    {
        return $this->address->fill([
            'city_id' => $params['city_id'] ?? null,
            'email' => $params['email'] ?? null,
            'phone' => $params['phone'],
            'street' => $params['street'] ?? null,
            'is_primary' => $params['is_primary'] ?? null
        ]);
    }

    /**
     * @return Model|false
     */
    public function assignAddressToOwner(HasAddressesInterface $owner, Address $address): Address
    {
        /** @var Address $address */
        $address =  $owner->addresses()->save($address);

        return $address;
    }
}
