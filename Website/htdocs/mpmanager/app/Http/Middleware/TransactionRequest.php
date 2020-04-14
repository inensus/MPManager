<?php

namespace App\Http\Middleware;

use App\Exceptions\VodacomHeartBeatException;
use App\Transaction\AirtelTransaction;
use App\Transaction\VodacomTransaction;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransactionRequest
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $transactionProvider = $request->attributes->get('transactionProcessor');
        try {
            $transactionProvider->validateRequest($request->getContent());
        } catch (VodacomHeartBeatException $ex) {
            // no need to do anything.
        } catch (Exception $e) {
            Log::critical('Transaction Validation failed', [
                'message' => $e->getMessage(),
                'content' => $request->getContent(),
                'id' => 'dfguige4fghz27dfvvjtz84'
            ]);
            return response()->json(['data' => ['message' => $e->getMessage()]], 401);
        }
        return $next($request);
    }
}
