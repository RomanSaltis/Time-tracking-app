@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Delete Task</h2>

        <form action="{{ route('tasks.destroy', $task) }}" method="POST">
            @csrf
            @method('DELETE')

            <p>Are you sure you want to delete this task?</p>

            <button type="submit" class="btn btn-danger">Delete</button>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
