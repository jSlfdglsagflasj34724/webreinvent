<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel To-Do List</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container mt-5">
    <h1>PHP - Simple To Do List App</h1>
    <div class="alert alert-danger d-none" id="error"></div>
    <div class="input-group mb-3">
        <input type="text" id="task-input" class="form-control" placeholder="Add new task">
        <div class="input-group-append">
            <button class="btn btn-primary" id="add-task-btn">Add Task</button>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Task</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="task-list">
        @foreach($tasks as $task)
            <tr data-id="{{ $task->id }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $task->name }}</td>
                <td>
                    <input type="checkbox" class="task-completed" {{ $task->status ? 'checked' : '' }}>
                </td>
                <td>
                    <button class="btn btn-danger delete-task-btn">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#add-task-btn').click(function() {
            let taskName = $('#task-input').val().trim();

            if (taskName === '') {
                $('#error').text('Task cannot be empty!').removeClass('d-none');
                return;
            }

            $.ajax({
                url: '/tasks',
                type: 'POST',
                data: {name: taskName},
                success: function(response) {
                    $('#error').addClass('d-none');
                    $('#task-list').append(`
                        <tr data-id="${response.task.id}">
                            <td>${response.task.id}</td>
                            <td>${response.task.name}</td>
                            <td><input type="checkbox" class="task-completed"></td>
                            <td><button class="btn btn-danger delete-task-btn">Delete</button></td>
                        </tr>
                    `);
                    $('#task-input').val('');
                },
                error: function(xhr) {
                    $('#error').text(xhr.responseJSON.error).removeClass('d-none');
                }
            });
        });

        $(document).on('change', '.task-completed', function() {
            let taskId = $(this).closest('tr').data('id');
            let completed = $(this).is(':checked')? 1 : 0;

            $.ajax({
                url: `/tasks/${taskId}`,
                type: 'PATCH',
                data: {completed: completed},
                success: function(response) {
                    console.log(response.success);
                }
            });
        });

        $(document).on('click', '.delete-task-btn', function() {
            if (!confirm('Are you sure to delete this task?')) return;

            let taskId = $(this).closest('tr').data('id');

            $.ajax({
                url: `/tasks/${taskId}`,
                type: 'DELETE',
                success: function(response) {
                    $(`tr[data-id="${taskId}"]`).remove();
                    console.log(response.success);
                }
            });
        });
    });
</script>
</body>
</html>
