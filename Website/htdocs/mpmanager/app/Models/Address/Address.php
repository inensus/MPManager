<?php

namespace App\Models\Address;

use App\Models\City;
use App\Models\BaseModel;
use App\Models\GeographicalInformation;

/**
 * Class Address
 *
 * @package App\Models\Address
 * @property string $email
 * @property string $phone
 * @property string $street
 * @property int city_id
 * @property int is_primary
 *
 */
class Address extends BaseModel
{
	protected $hidden = ['owner_id', 'owner_type'];
	public static $rules = [
		'city_id' => 'required|exists:cities,id',
	];

	public function city()
	{
		return $this->belongsTo(City::class);
	}

	// client & company
	public function owner()
	{
		return $this->morphTo();
	}

	public function geo()
	{
		return $this->belongsTo(GeographicalInformation::class);
	}


}
