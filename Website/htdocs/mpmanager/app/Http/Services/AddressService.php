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
    /**
     * @var Address
     */
    private $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    // fills the object and returns it without saving.
    public function instantiate(Array $params): Model
    {
        $a = $this->address;
        $a->city_id = $params['city_id'] ?? null;
        $a->email = $params['email'] ?? null;
        $a->phone = $params['phone'];
        $a->street = $params['street'] ?? null;
        $a->is_primary = $params['is_primary'] ?? null;
        return $a;
    }

    public function assignAddressToOwner(HasAddressesInterface $owner, $address)
    {
        return $owner->addresses()->save($address);
    }
}
