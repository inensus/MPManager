<?php

namespace App\Http\Controllers;

use App\Models\Transaction\VodacomTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VodacomTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        //get Transaction object
        $transactionData = request('transaction')->transaction;

        VodacomTransaction::create([
            'conversation_id' => $transactionData->conversationID,
            'originator_conversation_id' => $transactionData->originatorConversationID,
            'mpesa_receipt' => $transactionData->mpesaReceipt,
            'transaction_date' => $transactionData->transactionDate,
            'transaction_id' => $transactionData->transactionID,
        ])->transaction()->create([
            'amount' => $transactionData->amount,
            'sender' => $transactionData->initiator,
            'message' => $transactionData->accountReference,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
