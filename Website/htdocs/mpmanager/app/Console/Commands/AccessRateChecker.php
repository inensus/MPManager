<?php

namespace App\Console\Commands;

use App\Models\AccessRate\AccessRatePayment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AccessRateChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accessrate:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the "debt" field, based on "due_date" field';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // get all access-rate payments where due Date is <= today
        $accessRatePayments = AccessRatePayment::where('due_date', '<=', Carbon::now())->get();

        //iterate in unpaid acess-rates
        foreach ($accessRatePayments as $accessRatePayment) {
            $accessRate = $accessRatePayment->accessRate()->first();
            if ($accessRate === null) {
                continue;
            }
            $accessRatePayment->due_date = Carbon::now()->addDays($accessRate->period);
            //access-rate is defined
            if ($accessRate->amount > 0) {
                $accessRatePayment->debt += $accessRate->amount;
                $accessRatePayment->unpaid_in_row += 1;
            }
            $accessRatePayment->save();

            if ($accessRatePayment->unpaid_in_row > 1) {
                //unpaid in row = 2 notify call-center && send reminder to customer
                //unpaid =3 notify call-center && send warning to customer
                //unpaid =4 cutoff electricity
                //unpaid = 1 send customer a reminder
            }
        }
    }
}
