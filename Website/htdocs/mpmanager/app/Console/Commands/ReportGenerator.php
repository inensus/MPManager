<?php

namespace App\Console\Commands;

use App\Http\Controllers\Export\Reports;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReportGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:city-revenue {type} {--start-date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates city revenue reports';

    public $reports;

    /**
     * Create a new command instance.
     *
     * @param Reports $reports
     */
    public function __construct(Reports $reports)
    {
        parent::__construct();
        $this->reports = $reports;

    }


    /**
     * Execute the console command.
     */
    public function handle():void
    {
        $dateParam = $this->option('start-date');
        $typeParam = $this->argument('type');
        $toDay = new Carbon();
        $startDay = Carbon::now()->format('Y-m-d');
        if ($dateParam != "") {
            $toDay = Carbon::parse($this->option('start-date'))->format('Y-m-d');
        } else {
            $toDay = $toDay->subDays(1)->format('Y-m-d');
        }
        if ($typeParam == "weekly") {
            $startDay = Carbon::parse($toDay)->modify("last Monday")->format('Y-m-d');
        } elseif ($typeParam == "monthly") {
            $startDay = Carbon::parse($toDay)->modify("first day of this month")->format('Y-m-d');
        }

        $endDay = $toDay;
        $this->reports->generateWithJob($startDay, $endDay, $typeParam);

    }
}
