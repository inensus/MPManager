<?php

namespace App\Jobs;

use App\Models\Meter\MeterParameter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePiggyBankEntry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var MeterParameter
     */
    private $meterParameter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MeterParameter $meterParameter)
    {
        $this->meterParameter = $meterParameter;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $socialTariff = $this->meterParameter->tariff()->first()->socialTariff;
        if (!$socialTariff) {
            echo "meter has no social tariff should be deleted \n";
            //meter parameter has no social tariff

            //delete socialPiggyBankEntryForThatCustomer

            return;
        }
        if ($piggyBank = $this->meterParameter->socialTariffPiggyBank) {
            //update bank entry
            $piggyBank->savings = $socialTariff->initial_energy_budget;
        } else {
            CreatePiggyBankEntry::dispatch($this->meterParameter)
                ->allOnConnection('redis')
                ->onQueue(config('services.queues.misc'));
        }
    }
}
