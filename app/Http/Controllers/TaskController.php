<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View as FacadesView;


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
     * Generate a report based on the specified date range
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(Request $request): \Illuminate\Http\Response
    {
        // Retrieve tasks based on the date range
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $tasks = Task::whereBetween('created_at', [$startDate, $endDate])->get();

        // Create a new instance of the PDF class
        $pdf = new Dompdf();

        // Generate the HTML content from the view, passing the $tasks variable
        $html = FacadesView::make('tasks.report', ['tasks' => $tasks])->render();

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
