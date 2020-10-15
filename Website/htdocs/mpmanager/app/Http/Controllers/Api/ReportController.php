<?php


namespace App\Http\Controllers;


use App\Http\Resources\ApiResource;
use App\Models\Report;
use Illuminate\Http\Request;

/**
 * @group Reports
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

    public function download($id)
    {
        if (!$id) {
            return;
        }
        $report = $this->report->find($id);
        return response()->download($report->path);
    }

    /**
     * List
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
     * Weekly Reports
     * @urlParam startDate date
     * @urlParam endDate date
     * @param $startDate
     * @param $endDate
     * @return mixed
     */


    private function getWeeklyReports($startDate, $endDate)
    {
        return $this->report->where('type', 'weekly')->paginate(15);
    }

    /**
     * Monthly Reports
     * @urlParam startDate date
     * @urlParam endDate date
     * @param $startDate
     * @param $endDate
     * @return mixed
     */

    private function getMonthlyReports($startDate, $endDate)
    {
        return $this->report->where('type', 'monthly')
            ->paginate(15);
    }

    private function getAllReports($startDate, $endDate)
    {
        return $this->report->paginate(15);
    }
}
