@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Task List</div>

                    <div class="card-body">
                        <h2>Your Tasks</h2>

                        @if($tasks->isEmpty())
                            <p>No tasks found.</p>
                        @else
                            <ul>
                                @foreach($tasks as $task)
                                    <li>{{ $task->title }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
