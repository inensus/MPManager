<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MainSettings
 *
 * @package App\Models
 * @property int $id
 * @property string $site_title
 * @property string $company_name
 * @property string $currency
 * @property string $country
 * @property string $language
 * @property float $vat_energy
 * @property float $vat_appliance
 * */
class MainSettings extends Model
{
    protected $fillable = [ 'site_title', 'company_name', 'currency', 'country', 'language', 'vat_energy', 'vat_appliance'];
}
