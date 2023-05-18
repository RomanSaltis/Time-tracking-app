@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Task</h2>

        <form method="POST" action="{{ route('tasks.update', $task->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ $task->title }}" required>
            </div>

            <div class="mb-3">
                <label for="comment" class="form-label">Comment</label>
                <textarea class="form-control" id="comment" name="comment" rows="4">{{ $task->comment }}</textarea>
            </div>

            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ $task->date->format('Y-m-d') }}" required>
            </div>

            <div class="mb-3">
                <label for="time_spent" class="form-label">Time Spent (in minutes)</label>
                <input type="number" class="form-control" id="time_spent" name="time_spent" value="{{ $task->time_spent }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Task</button>
        </form>
    </div>
@endsection
