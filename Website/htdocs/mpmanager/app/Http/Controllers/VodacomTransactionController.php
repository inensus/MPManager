<?php

namespace App\Http\Controllers;

use App\Models\Transaction\VodacomTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group   Transactions
 * Class VodacomTransactionController
 * @package App\Http\Controllers
 */
class VodacomTransactionController extends Controller
{
    /**
     * Create
     * Create a new vodacom transaction
     * @bodyParam conversation_id int
     * @bodyParam originator_conversation_id int
     * @bodyParam mpesa_receipt int
     * @bodyParam transaction_date date
     * @bodyParam transaction_id int
     * @bodyParam amount double
     * @bodyParam sender string
     * @bodyParam message string
     *
     * @param Request $request
     *
     * @return void
     */
    public function store(Request $request): void
    {

        //get Transaction object
        $transactionData = request('transaction')->transaction;

        VodacomTransaction::create(
            [
            'conversation_id' => $transactionData->conversationID,
            'originator_conversation_id' => $transactionData->originatorConversationID,
            'mpesa_receipt' => $transactionData->mpesaReceipt,
            'transaction_date' => $transactionData->transactionDate,
            'transaction_id' => $transactionData->transactionID,
            ]
        )->transaction()->create(
            [
                'amount' => $transactionData->amount,
                'sender' => $transactionData->initiator,
                'message' => $transactionData->accountReference,
                ]
        );
    }
}
