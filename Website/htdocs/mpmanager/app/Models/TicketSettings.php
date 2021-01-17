<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketSettings
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $api_token
 * @property string $api_url
 * @property string $api_key
 * */

class TicketSettings extends Model
{
    protected $fillable = [ 'name', 'api_token', 'api_url', 'api_key'];
}
