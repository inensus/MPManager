<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 2019-03-14
 * Time: 11:50
 */

namespace App\Http\Services;


use App\Models\Meter\Meter;
use App\Models\Meter\MeterToken;
use App\Models\Transaction\AirtelTransaction;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\VodacomTransaction;
use Illuminate\Support\Facades\DB;

class RevenueService
{

    /**
     * @var Meter
     */
    private $meter;
    /**
     * @var MeterToken
     */
    private $meter_token;

    /**
     * RevenueService constructor.
     *
     * @param Meter $meter
     */
    public function __construct(Meter $meter, MeterToken $meter_token)
    {
        $this->meter = $meter;
        $this->meter_token = $meter_token;
    }


    public function getClustersRevenueByPeriod($clusterId, $period, $periodType, $connectionType = null)
    {

        if ($periodType === 'weekly' || $periodType === 'weekMonth') {
            return $this->getMeterTransactionsByWeeklyPeriod($clusterId, $period);
        }
        return $this->getClusterTransactionsByMonthlyPeriod($clusterId, $period, $connectionType);
    }

    public function getMiniGridsRevenueByPeriod($miniGridId, $period, $periodType)
    {

        if ($periodType === 'weekly' || $periodType === 'weekMonth') {
            return $this->getMeterTransactionsByWeeklyPeriod($miniGridId, $period);
        }
        return $this->getMiniGridTransactionsByMonthlyPeriod($miniGridId, $period);
    }


    public function getMetersRevenueByPeriod($meters, $period, $periodType, $connectionType = null)
    {
        /* returns data like
        Month-Year	Week	Revenue
        2019-1		1		34233
        2019-1		2		34233
        2019-1		3		34233*/

        if ($periodType === 'weekly' || $periodType === 'weekMonth') {
            return $this->getMeterTransactionsByWeeklyPeriod($meters, $period);
        }
        return $this->getClusterTransactionsByMonthlyPeriod($meters, $period, $connectionType);


    }

    private function getMeterTransactionsByWeeklyPeriod($meters, $period)
    {
        $revenue = $this->meter->transactions()
            ->selectRaw('DATE_FORMAT(created_at,\'%Y-%m\') as period ,DATE_FORMAT(created_at,\'%Y-%u\')  as week, SUM(amount) as revenue')
            ->where(function ($q) {
                $q->where('original_transaction_type', 'airtel_transaction');
                $q->whereHas('originalAirtel', function ($q) {
                    $q->where('status', 1);
                });
            })
            ->orWhere(function ($q) {
                $q->where('original_transaction_type', 'vodacom_transaction');
                $q->whereHas('originalVodacom', function ($q) {
                    $q->where('status', 1);
                });
            })
            ->whereIn('message', $meters->pluck('serial_number'))
            ->whereBetween('created_at', $period)
            ->groupBy(DB::raw('DATE_FORMAT(created_at,\'%Y-%m\'),WEEKOFYEAR(created_at)'))->get();


        return $revenue;
    }

    public function getMeterSoldEnergy($meters, $period)
    {
        return $this->meter_token
            ->selectRaw('COUNT(id) as amount, SUM(energy) as energy')
            ->whereIn('meter_id', $meters->pluck('id'))
            ->whereBetween('created_at', $period)
            ->get();

    }

    public function getMeterTransactions($meters, $period)
    {
        $revenue = $this->meter->transactions()
            ->selectRaw('COUNT(id) as amount, SUM(amount) as revenue')
            ->where(function ($q) {
                $q->where('original_transaction_type', 'airtel_transaction');
                $q->whereHas('originalAirtel', function ($q) {
                    $q->where('status', 1);
                });
            })
            ->orWhere(function ($q) {
                $q->where('original_transaction_type', 'vodacom_transaction');
                $q->whereHas('originalVodacom', function ($q) {
                    $q->where('status', 1);
                });
            })
            ->whereIn('message', $meters->pluck('serial_number'))
            ->whereBetween('created_at', $period)->get();
        return $revenue;
    }


    private function getClusterTransactionsByMonthlyPeriod($clusterId, $period, $connectionType = null)
    {
        return Transaction::selectRaw('DATE_FORMAT(created_at,\'%Y-%m\') as period , SUM(amount) as revenue')
            ->whereHas('meter', function ($q) use ($clusterId, $connectionType) {
                $q->whereHas('meterParameter', function ($q) use ($clusterId, $connectionType) {
                    if ($connectionType) {
                        $q->where('connection_type_id', $connectionType);
                    }
                    $q->whereHas('address', function ($q) use ($clusterId) {
                        $q->whereHas('city', function ($q) use ($clusterId) {
                            $q->where('cluster_id', $clusterId);
                        });
                    });
                });
            })->whereHasMorph('originalTransaction', [VodacomTransaction::class, AirtelTransaction::class],
                static function ($q) {
                    $q->where('status', 1);
                })
            ->whereBetween('created_at', $period)
            ->groupBy(DB::raw('DATE_FORMAT(created_at,\'%Y-%m\')'))->get();
    }


    private function getMiniGridTransactionsByMonthlyPeriod($miniGridId, $period)
    {
        return Transaction::selectRaw('DATE_FORMAT(created_at,\'%Y-%m\') as period , SUM(amount) as revenue')
            ->whereHas('meter', function ($q) use ($miniGridId) {
                $q->whereHas('meterParameter', function ($q) use ($miniGridId) {
                    $q->whereHas('address', function ($q) use ($miniGridId) {
                        $q->whereHas('city', function ($q) use ($miniGridId) {
                            $q->where('mini_grid_id', $miniGridId);
                        });
                    });
                });
            })->whereHasMorph('originalTransaction', [VodacomTransaction::class, AirtelTransaction::class],
                static function ($q) {
                    $q->where('status', 1);
                })
            ->whereBetween('created_at', $period)
            ->groupBy(DB::raw('DATE_FORMAT(created_at,\'%Y-%m\')'))->get();
    }

}
