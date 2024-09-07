<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h2 class="my-4">Todo List</h2>

        <div class="input-group mb-3">
            <input type="text" id="task-input" class="form-control" placeholder="Add a new task">
            <button class="btn btn-primary" id="add-task-btn">Add Task</button>
        </div>

        <ul id="task-list" class="list-group"></ul>

        <button class="btn btn-secondary mt-4" id="show-all-btn">Show All Tasks</button>
    </div>

    <script>
        $(document).ready(function() {
            // CSRF Token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Fetch and display tasks
            function fetchTasks() {
                $.get('/tasks', function(tasks) {
                    $('#task-list').empty();
                    tasks.forEach(task => {
                        if (!task.is_completed) {
                            $('#task-list').append(
                                `<li class="list-group-item d-flex justify-content-between align-items-center" data-id="${task.id}">
                                    <input type="checkbox" class="task-checkbox">
                                    ${task.task}
                                    <button class="btn btn-danger btn-sm delete-task-btn">Delete</button>
                                </li>`
                            );
                        }
                    });
                });
            }

            // Initial fetch of tasks
            fetchTasks();

            // Add new task
            $('#add-task-btn').click(function() {
                let task = $('#task-input').val().trim();
                if (task === '') {
                    alert('Task cannot be empty!');
                    return;
                }

                $.post('/tasks', { task: task }, function(response) {
                    $('#task-input').val('');
                    fetchTasks();
                }).fail(function(xhr) {
                    alert(xhr.responseJSON.message || 'Task already exists.');
                });
            });

            // Mark task as completed
            $(document).on('click', '.task-checkbox', function() {
                let taskId = $(this).closest('li').data('id');
                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: 'PUT',
                    success: function(response) {
                        fetchTasks();
                    }
                });
            });

            // Delete task with confirmation
            $(document).on('click', '.delete-task-btn', function() {
                if (confirm('Are you sure to delete this task?')) {
                    let taskId = $(this).closest('li').data('id');
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: 'DELETE',
                        success: function(response) {
                            fetchTasks();
                        }
                    });
                }
            });

            // Show all tasks
            $('#show-all-btn').click(function() {
                $.get('/tasks', function(tasks) {
                    $('#task-list').empty();
                    tasks.forEach(task => {
                        $('#task-list').append(
                            `<li class="list-group-item d-flex justify-content-between align-items-center" data-id="${task.id}">
                                <input type="checkbox" class="task-checkbox" ${task.is_completed ? 'checked' : ''}>
                                ${task.task}
                                <button class="btn btn-danger btn-sm delete-task-btn">Delete</button>
                            </li>`
                        );
                    });
                });
            });
        });
    </script>
</body>
</html>
