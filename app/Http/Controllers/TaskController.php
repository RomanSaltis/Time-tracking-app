<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource for user.
     *
     * @return View
     */
    public function index(): View
    {
        if (!is_superadmin(user())){
            $tasks = Task::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
        }else{
            $tasks = Task::all();
        }
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('tasks.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'comment' => 'nullable',
            'date' => 'required|date',
            'time_spent' => 'required|integer',
        ]);

        $task = new Task();
        $task->title = $validatedData['title'];
        $task->comment = $validatedData['comment'];
        $task->date = $validatedData['date'];
        $task->time_spent = $validatedData['time_spent'];
        $task->user_id = auth()->id();
        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param Task $task
     * @return View
     * @throws AuthorizationException
     */
    public function show(Task $task): View
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Task $task
     * @return View
     * @throws AuthorizationException
     */
    public function edit(Task $task): View
    {
        $this->authorize('update', $task);
        return view('tasks.edit', compact('task'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);
        $validatedData = $request->validate([
            'title' => 'required',
            'comment' => 'nullable',
            'date' => 'required|date',
            'time_spent' => 'required|integer',
        ]);

        $task->title = $validatedData['title'];
        $task->comment = $validatedData['comment'];
        $task->date = $validatedData['date'];
        $task->time_spent = $validatedData['time_spent'];
        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage
     * @param Task $task
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Task $task):RedirectResponse
    {
        $this->authorize('delete', $task);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    /**
     * Generate a report based on the specified date range and format.
     *
     * @param Request $request
     * @return mixed
     */
    public function generateReport(Request $request)
    {
        // Retrieve tasks based on the date range
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if (!is_superadmin(user())) {
            $tasks = collect(user()->tasks()->whereBetween('created_at', [$startDate, $endDate])->get())->toArray();
        } else {
            $tasks = collect(Task::whereBetween('created_at', [$startDate, $endDate])->get())->toArray();
        }

        // Determine the desired output format (PDF, CSV, or Excel)
        $format = $request->input('format', 'pdf');

        // Calculate the total time
        $totalTime = $this->calculateTotalTime($tasks);

        // Generate the report file based on the format
        switch ($format) {
            case 'csv':
                return $this->generateCsvReport($tasks, $totalTime);
            case 'excel':
                return $this->generateExcelReport($tasks, $totalTime);
            case 'pdf':
            default:
                return $this->generatePdfReport($tasks, $totalTime);
        }
    }

    /**
     * Generate a CSV report.
     *
     * @param array $data
     * @param int $totalTime
     * @return StreamedResponse
     */
    protected function generateCsvReport($data, $totalTime)
    {
        $fileName = 'report_' . date('YmdHis') . '.csv';

        $callback = function () use ($data, $totalTime) {
            $file = fopen('php://output', 'w');

            // Write the header row
            fputcsv($file, array_keys($data[0]));

            // Write the data rows
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            // Write the total time row
            fputcsv($file, ['Total Time', $totalTime]);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Generate an Excel report.
     *
     * @param array $data
     * @param int $totalTime
     * @return mixed
     * @throws Exception
     */
    protected function generateExcelReport($data, $totalTime)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the headers
        $headers = array_keys($data[0]);
        $sheet->fromArray($headers, null, 'A1');

        // Set the data rows
        $rowData = [];
        foreach ($data as $item) {
            $rowData[] = array_values($item);
        }
        $sheet->fromArray($rowData, null, 'A2');

        // Set the total time row
        $totalTimeRow = ['Total Time', $totalTime];
        $sheet->fromArray([$totalTimeRow], null, 'A' . (count($rowData) + 2));

        $fileName = 'report_' . date('YmdHis') . '.xlsx';
        $filePath = storage_path('app/public/reports/' . $fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    /**
     * Generate a PDF report.
     *
     * @param array $tasks
     * @param int $totalTime
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function generatePdfReport($tasks, $totalTime)
    {
        $pdf = new Dompdf();

        // Transform the array items to have the 'id' property
        $tasks = array_map(function ($task) {
            return is_array($task) ? (object) $task : $task;
        }, $tasks);

        // Generate the HTML content from the view, passing the $tasks and $totalTime variables
        $html = view('tasks.report', compact('tasks', 'totalTime'))->render();

        // Load the HTML content into the PDF
        $pdf->loadHtml($html);

        // (Optional) Set Dompdf options, if needed
        // $pdf->setOptions(['...']);

        // Render the PDF
        $pdf->render();

        $fileName = 'report_' . date('YmdHis') . '.pdf';

        // Get the PDF output
        $output = $pdf->output();

        // Create the response with PDF content type
        $response = response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);

        // Add the custom header with the total time
        $response->header('x-total-time', $totalTime);

        return $response;
    }

    /**
     * Calculate the total time from the given data.
     *
     * @param array $data
     * @return int
     */
    protected function calculateTotalTime($data)
    {
        $totalTime = 0;

        foreach ($data as $row) {
            $timeSpent = isset($row['time_spent']) ? $row['time_spent'] : 0;
            $totalTime += $timeSpent;
        }

        return $totalTime;
    }
}
