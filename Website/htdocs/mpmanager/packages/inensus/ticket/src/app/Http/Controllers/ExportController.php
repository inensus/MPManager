<?php


namespace Inensus\Ticket\Http\Controllers;


use Illuminate\Http\Request;
use Inensus\Ticket\Http\Resources\TicketResource;
use Inensus\Ticket\Models\Label;
use Inensus\Ticket\Models\OutsourceReport;
use Inensus\Ticket\Models\Ticket;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController
{
    /**
     * @var Ticket
     */
    private $ticket;
    /**
     * @var Label
     */
    private $label;
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;
    /**
     * @var OutsourceReport
     */
    private $outsourceReport;


    public function __construct(
        Ticket $ticket,
        Label $label,
        Spreadsheet $spreadsheet,
        OutsourceReport $outsourceReport
    ) {
        $this->ticket = $ticket;
        $this->label = $label;
        $this->spreadsheet = $spreadsheet;
        $this->outsourceReport = $outsourceReport;
    }

    /**
     * A list of stored book keeping data
     *
     * @return TicketResource
     */
    public function index(): TicketResource
    {
        $reports = $this->outsourceReport->get();
        return new TicketResource($reports);
    }

    /**
     * Generates a book keeping file and stores it
     *
     * @param Request $request
     *
     * @return TicketResource
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function outsource(Request $request): TicketResource
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        $tickets = $this->ticket
            ::with('outsource', 'assignedTo', 'category')
            ->whereHas('category', static function ($q) {
                $q->where('out_source', 1);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        //create excel sheet

        $fileName = 'Outsourcing - ' . $startDate . ' - ' . $endDate . ' .xlsx';

        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle('payments - ' . date('Y-m', strtotime($startDate)));

        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Amount');
        $sheet->setCellValue('D1', 'Category');

        $row = 3;
        foreach ($tickets as $t) {
            $sheet->setCellValue('A' . $row, $t->assignedTo->user_name);
            $sheet->setCellValue('B' . $row, $t->created_at);
            $sheet->setCellValue('C' . $row, $t->outsource->amount);
            $sheet->setCellValue('D' . $row, $t->category->label_name);
        }

        $writer = new Xlsx($this->spreadsheet);
        try {
            $writer->save(storage_path('./outsourcing/' . $fileName));
        } catch (Exception $e) {
            echo 'error' . $e->getMessage();
        }

        $this->outsourceReport->date = date('Y-m', strtotime($startDate));
        $this->outsourceReport->path = storage_path('./outsourcing/' . $fileName);
        $this->outsourceReport->save();
        return new TicketResource($this->outsourceReport);
    }

    public function download($id): BinaryFileResponse
    {
        $report = $this->outsourceReport->find($id);
        return response()->download($report->path);
    }


}
