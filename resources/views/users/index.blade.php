@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>User List</h1>

        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a href="{{ route('users.show', $user) }}" class="btn btn-primary btn-sm">View</a>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-info btn-sm">Edit</a>
                        <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
