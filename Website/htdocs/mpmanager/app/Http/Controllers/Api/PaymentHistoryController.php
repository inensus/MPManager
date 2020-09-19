<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 31.07.18
 * Time: 10:47
 */

namespace App\Http\Controllers;


use App\Http\Resources\ApiResource;
use App\Models\AccessRate\AccessRatePayment;
use App\Models\PaymentHistory;
use App\Models\Person\Person;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DatePeriod;
use Illuminate\Http\Request;
use function count;

class PaymentHistoryController
{
    /**
     * @var PaymentHistory
     */
    private $history;

    /**
     * PaymentHistoryController constructor.
     * @param PaymentHistory $history
     */
    public function __construct(PaymentHistory $history)
    {
        $this->history = $history;
    }

    public function show(int $payerId, string $period, $limit = null, $order = 'ASC')
    {
        $period = strtoupper($period);

        switch ($period) {
            case 'D':
                $period = 'Day(created_at), Month(created_at), Year(created_at)';
                break;
            case 'W':
                $period = 'Week(created_at), Year(created_at)';
                break;
            case 'M':
                $period = 'Month(created_at), Year(created_at)';
                break;
            default:
                $period = 'Year(created_at)';
                break;
        }

        $payments = (new PaymentHistory)->getFlow('person', $payerId, $period, $limit, $order);


        $flowList = [];

        foreach ($payments as $payment) {
            $flowList[$payment['aperiod']][$payment['payment_type']] = $payment['amount'];
        }

        return $flowList;
    }
    public function showForAgentCustomers( string $period, $limit = null, $order = 'ASC')
    {
        $agent = request()->attributes->get('user');
        $period = strtoupper($period);

        switch ($period) {
            case 'D':
                $period = 'Day(payment_histories.created_at), Month(payment_histories.created_at), Year(payment_histories.created_at)';
                break;
            case 'W':
                $period = 'Week(payment_histories.created_at), Year(payment_histories.created_at)';
                break;
            case 'M':
                $period = 'Month(payment_histories.created_at), Year(payment_histories.created_at)';
                break;
            default:
                $period = 'Year(payment_histories.created_at)';
                break;
        }

        $payments = (new PaymentHistory)->getFlowForAgentCustomers('person', $agent->id, $period, $limit, $order);


        $flowList = [];

        foreach ($payments as $payment) {
            $flowList[$payment['aperiod']][$payment['payment_type']] = $payment['amount'];
        }

        return $flowList;
    }


    public function getPaymentPeriod($personId)
    {
        $person = Person::find($personId);
        $lastTransactions = $person->transactions()->latest()->take(10)->get();
        $difference = 'no data available';
        $lastTransactionDate = null;
        if (count($lastTransactions)) {
            $lastTransactionDate = $newest = $lastTransactions[0]->created_at;
            $newest = new Carbon($newest);
            $lastTransactionDate = $newest->diffInDays(Carbon::now()) . ' days ago';
            $eldest = new Carbon($lastTransactions[count($lastTransactions) - 1]->created_at);
            $difference = $eldest->diffInDays($newest) . ' days';
        }
        return new ApiResource(['difference' => $difference, 'lastTransaction' => $lastTransactionDate]);
    }

    public function byYear(int $personId, int $year = null)
    {
        $year = $year ?? (int)date('Y');
        $payments = (new PaymentHistory)->getPaymentFlow('person', $personId, $year);
        $paymentFlow = array_fill(0, 11, 0);
        foreach ($payments as $payment) {
            $paymentFlow[$payment['month'] - 1] = $payment['amount'];
        }
        return $paymentFlow;
    }


    /***
     * if the person has any debts to the system
     * @param int $personId
     */
    public function debts($personId)
    {
        $ad = 0;
        $accessDebt = Person::with('meters.meter.accessRatePayment')->find($personId);
        foreach ($accessDebt->meters as $m) {
            $ad += $m->meter->accessRatePayment->debt;
        }


        $deferredDebt = 0;
        return new ApiResource(['access_rate' => $ad, 'deferred' => $deferredDebt]);

    }


    public function getPaymentRange(): ApiResource
    {
        $begin = request('begin'); // Y-m-d
        $end = request('end'); // Y-m- d
        //create a sequence of dates
        $period = new DatePeriod(
            Carbon::parse($begin),
            CarbonInterval::day(),
            Carbon::parse($end . ' 00:01')
        );
        $result = [];
        foreach ($period as $p) {
            $result[(new Carbon($p))->toDateString()] = ['date' => (new Carbon($p))->toDateString(), 'amount' => 0];
        }
        $payments = $this->history->getOverview($begin, $end);
        foreach ($payments as $p) {
            $result[$p['dato']] = ['date' => $p['dato'], 'amount' => $p['total']];
        }
        return new ApiResource(array_values($result));
    }
}
