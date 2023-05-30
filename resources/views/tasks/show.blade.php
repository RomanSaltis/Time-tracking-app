@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Task Details</h1>

        <div class="card">
            <div class="card-header">
                <h5>Task Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Title:</strong> {{ $task->title }}</p>
                <p><strong>Comment:</strong> {{ $task->comment }}</p>
                <p><strong>Time Spent:</strong> {{ $task->time_spent }}</p>
                <p><strong>Created At:</strong> {{ $task->created_at }}</p>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Back to Task List</a>
            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary">Edit Task</a>
            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this task?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Task</button>
            </form>
        </div>
    </div>
@endsection
