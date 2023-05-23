@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>User Details</h1>

        <div class="card">
            <div class="card-header">
                <h5>User Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Created At:</strong> {{ $user->created_at }}</p>
                <p><strong>Updated At:</strong> {{ $user->updated_at }}</p>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to User List</a>
            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete User</button>
            </form>
        </div>
    </div>
@endsection
