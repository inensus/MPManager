<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 2019-03-14
 * Time: 11:50
 */

namespace App\Http\Services;

use App\Models\Meter\MeterToken;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RevenueService
{
    public function __construct(private MeterToken $meter_token, private Transaction $transaction)
    {
    }

    public function clustersRevenueByPeriod(int $clusterId, array $period, $periodType, int $connectionType = null): Collection
    {
        if ($periodType === 'weekly' || $periodType === 'weekMonth') {
            return $this->getMeterTransactionsByWeeklyPeriod($clusterId, $period);
        }
        return $this->getClusterTransactionsByMonthlyPeriod($clusterId, $period, $connectionType);
    }

    public function getMiniGridsRevenueByPeriod(int $miniGridId, array $period, string $periodType): Collection
    {

        if ($periodType === 'weekly' || $periodType === 'weekMonth') {
            return $this->getMeterTransactionsByWeeklyPeriod($miniGridId, $period);
        }
        return $this->getMiniGridTransactionsByMonthlyPeriod($miniGridId, $period);
    }

    private function getMeterTransactionsByWeeklyPeriod($meters, $period): Collection
    {
        return $this->transaction->newQuery()
            ->selectRaw('DATE_FORMAT(created_at,\'%Y-%m\') as period ,DATE_FORMAT(created_at,\'%Y-%u\') ' .
                ' as week, SUM(amount) as revenue')
            ->whereHasMorph(
                'originalTransaction',
                '*',
                static function ($q) {
                    $q->where('status', 1);
                }
            )
            ->whereIn('message', $meters->pluck('serial_number'))
            ->whereBetween('created_at', $period)
            ->groupBy(DB::raw('DATE_FORMAT(created_at,\'%Y-%m\'),WEEKOFYEAR(created_at)'))->get();
    }

    public function getMeterSoldEnergy(Collection $meters, array $period): ?Collection
    {
        return $this->meter_token->newQuery()
            ->selectRaw('COUNT(id) as amount, SUM(energy) as energy')
            ->whereIn('meter_id', $meters->pluck('id'))
            ->whereBetween('created_at', $period)
            ->get();
    }

    public function getMeterTransactions($meters, array $period): Collection
    {

        return $this->transaction->newQuery()
            ->selectRaw('COUNT(id) as amount, SUM(amount) as revenue')
            ->whereHasMorph(
                'originalTransaction',
                '*',
                static function ($q) {
                    return $q->where('status', 1);
                }
            )
            ->whereIn('message', $meters->pluck('serial_number'))
            ->whereBetween('created_at', $period)->get();
    }


    private function getClusterTransactionsByMonthlyPeriod($clusterId, $period, $connectionType = null): Collection
    {
        return $this->transaction->newQuery()
            ->selectRaw('DATE_FORMAT(created_at,\'%Y-%m\') as period , SUM(amount) as revenue')
            ->whereHas(
                'meter',
                function ($q) use ($clusterId, $connectionType) {
                    $q->whereHas(
                        'meterParameter',
                        function ($q) use ($clusterId, $connectionType) {
                            if ($connectionType) {
                                $q->where('connection_type_id', $connectionType);
                            }
                            $q->whereHas(
                                'address',
                                function ($q) use ($clusterId) {
                                    $q->whereHas(
                                        'city',
                                        function ($q) use ($clusterId) {
                                            $q->where('cluster_id', $clusterId);
                                        }
                                    );
                                }
                            );
                        }
                    );
                }
            )->whereHasMorph(
                'originalTransaction',
                '*',
                static function ($q) {
                    $q->where('status', 1);
                }
            )
            ->whereBetween('created_at', $period)
            ->groupBy(DB::raw('DATE_FORMAT(created_at,\'%Y-%m\')'))->get();
    }


    private function getMiniGridTransactionsByMonthlyPeriod(int $miniGridId, array $period): Collection
    {
        return $this->transaction->newQuery()
            ->selectRaw('DATE_FORMAT(created_at,\'%Y-%m\') as period , SUM(amount) as revenue')
            ->whereHas(
                'meter',
                function ($q) use ($miniGridId) {
                    $q->whereHas(
                        'meterParameter',
                        function ($q) use ($miniGridId) {
                            $q->whereHas(
                                'address',
                                function ($q) use ($miniGridId) {
                                    $q->whereHas(
                                        'city',
                                        function ($q) use ($miniGridId) {
                                            $q->where('mini_grid_id', $miniGridId);
                                        }
                                    );
                                }
                            );
                        }
                    );
                }
            )->whereHasMorph(
                'originalTransaction',
                '*',
                static function ($q) {
                    $q->where('status', 1);
                }
            )
            ->whereBetween('created_at', $period)
            ->groupBy(DB::raw('DATE_FORMAT(created_at,\'%Y-%m\')'))->get();
    }
}
