@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Tasks</h2>

        <a href="{{ route('tasks.create') }}" class="btn btn-primary mb-3">Create Task</a>

        <form action="{{ route('tasks.report') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Generate Report</button>
        </form>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Comment</th>
                <th>Time Spent</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->comment }}</td>
                    <td>{{ $task->time_spent }}</td>
                    <td>{{ $task->created_at }}</td>
                    <td>
                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary">Edit</a>
                        <!-- Add a delete button or form for deleting the task -->
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
