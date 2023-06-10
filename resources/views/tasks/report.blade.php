<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<h2>Task Report</h2>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Comment</th>
        <th>Time Spent/min</th>
        <th>Created At</th>
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
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th colspan="3">Total Time/min:</th>
        <td colspan="2">{{ $totalTime }}</td>
    </tr>
    </tfoot>
</table>
</body>
</html>
