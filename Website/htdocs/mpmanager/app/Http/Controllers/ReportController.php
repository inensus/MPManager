<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\Report;
use Illuminate\Http\Request;

/**
 * @group   Report
 * Class ReportController
 * @package App\Http\Controllers
 */
class ReportController
{
    /**
     * @var Report
     */
    private $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Download a Report
     */
    public function download($id)
    {
        if (!$id) {
            return;
        }
        $report = $this->report->find($id);
        return response()->download($report->path);
    }

    /**
     * Periodic List
     * A list of the reports for between specified dates.
     * @responseFile responses/reports/reports.list.json
     * @param Request $request
     * @return ApiResource
     */
    public function index(Request $request): ApiResource
    {
        $type = $request->get('type');
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        $reports = null;
        switch ($type) {
            case 'weekly':
                $reports = $this->getWeeklyReports($startDate, $endDate);
                break;
            case 'monthly':
                $reports = $this->getMonthlyReports($startDate, $endDate);
                break;
            default:
                $reports = $this->getAllReports($startDate, $endDate);
                break;
        }
        return new ApiResource($reports);
    }

    /**
     * Weekly Detail
     * Details of the reports between the specified dates.
     * @bodyParam startDate date required
     * @bodyParam endDate date required
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    private function getWeeklyReports($startDate, $endDate)
    {
        return $this->report->where('type', 'weekly')->paginate(15);
    }

    /**
     * Monthly Detail
     * Details of the reports between the specified dates.
     * @bodyParam startDate date required
     * @bodyParam endDate date required
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    private function getMonthlyReports($startDate, $endDate)
    {
        return $this->report->where('type', 'monthly')
            ->paginate(15);
    }

    /**
     * List
     * A list of the all Reports.
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    private function getAllReports($startDate, $endDate)
    {
        return $this->report->paginate(15);
    }
}
