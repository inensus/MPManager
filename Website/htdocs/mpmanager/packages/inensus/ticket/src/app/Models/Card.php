<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 20.08.18
 * Time: 15:00
 */

namespace Inensus\Ticket\Models;

/**
 * Class Ticket
 * @package Inensus\Ticket\Models
 * @property int $id
 * @property string $card_id
 * @property int $status
 * @property string $owner_type
 * @property int $owner_id
 */
class Card extends BaseModel
{

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function owner()
    {
        return $this->morphTo('owner');
    }

}
