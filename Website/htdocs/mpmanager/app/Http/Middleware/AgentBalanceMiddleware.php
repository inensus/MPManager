<?php

namespace App\Http\Middleware;

use App\Exceptions\AgentRiskBalanceExceeded;
use App\Exceptions\DownPaymentNotFoundException;
use App\Exceptions\TransactionAmountNotFoundException;
use Closure;

class AgentBalanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeName = request()->route()->getName();
        $agent = auth('agent_api')->user();
        $commission = $agent->commission()->first();
        $amount = $agent->balance;
        if ($routeName === 'agent-sell-appliance') {
            if ($downPayment = $request->input('down_payment')) {
                $amount -= $downPayment;
            } else {
                throw  new DownPaymentNotFoundException('DownPayment not found');
            }

        }
        if ($routeName === 'agent-transaction') {
            if ($transactionAmount = $request->input('amount')) {
                $amount -= $transactionAmount;
            } else {
                throw  new TransactionAmountNotFoundException('Transaction amount not found');
            }


        }

        if ($amount < $commission->risk_balance) {
            throw  new AgentRiskBalanceExceeded('Risk balance exceeded');
        }
        return $next($request);
    }
}
