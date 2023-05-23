<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
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
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function generateReport(Request $request)
    {
        // Retrieve tasks based on the date range
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $tasks = Task::whereBetween('created_at', [$startDate, $endDate])->get();

        // Create a new instance of the PDF class
        $pdf = new Dompdf();

        // Generate the HTML content from the view, passing the $tasks variable
        $html = View::make('tasks.report', ['tasks' => $tasks])->render();

        // Load the HTML content into the PDF
        $pdf->loadHtml($html);

        // (Optional) Set Dompdf options, if needed
        // $pdf->setOptions(['...']);

        // Render the PDF
        $pdf->render();

        // Generate the file name for the PDF
        $fileName = 'report_' . date('YmdHis') . '.pdf';

        // Get the PDF content
        $output = $pdf->output();

        // Create a download response
        return Response::make($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
