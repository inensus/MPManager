<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 2019-03-13
 * Time: 19:22
 */

namespace App\Http\Services;

use App\Models\Transaction\Transaction;

class TransactionService
{

    public function totalClusterTransactions(int $clusterId, array $range)
    {
        return   Transaction::query()->whereHas(
            'meter',
            function ($q) use ($clusterId) {
                $q->whereHas(
                    'meterParameter',
                    function ($q) use ($clusterId) {
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
            ->whereBetween('created_at', $range)
            ->sum('amount');
    }
}
