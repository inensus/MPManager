<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentReceiptDetail extends BaseModel
{
    public function receipt()
    {
        return $this->belongsTo(AgentReceipt::Class, 'agent_receipt_id');
    }
}
