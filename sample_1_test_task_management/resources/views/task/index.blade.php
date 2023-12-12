@php use App\Models\Task;use Illuminate\Database\Eloquent\Collection; @endphp
<?php
/**
 * @var Collection<Task> $tasks
 */
?>
@extends('layout')

@section('content')
    <script src="https://code.jquery.com/jquery-2.2.0.js"></script>
        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <div class="mb-4">
        <a href="{{route('task_create')}}" class="btn btn-primary">Create</a>
    </div>
    <select id="projectId" class="form-control mb-4">
        <option value="">Project not selected</option>
        @foreach($projects as $id => $name)
            <option value="{{$id}}" {{$projectId === $id ? 'selected' : ''}}>{{$name}}</option>
        @endforeach
    </select>
    @if($tasks->isEmpty())
        <div>
            Empty list
        </div>
    @else
        <h2 class="h-2">Task list</h2>
        <form action="" id="draggable" method="post">
            @csrf
            @foreach($tasks as $task)
                <div class="card mb-1 draggable" id="{{$task->id}}">
                    <div class="card-body">
                        <input type="hidden" name="taskPriorities[{{$task->id}}]" value="{{$task->priority}}">
                        <div class="mb-2">{{$task->name}}</div>
                        <div>
                            <a href="{{route('task_edit', ['id' => $task->id])}}" class="btn btn-success">Edit</a>
                            <a href="{{route('task_delete', ['id' => $task->id])}}" class="btn btn-danger">Delete</a>
                        </div>
                    </div>
                </div>
            @endforeach
            <button class="btn btn-primary" type="submit">Save priorities</button>
        </form>
    @endif

    <script>
        $(function() {
            $('#draggable').sortable({
                update: function() {
                    $('.draggable').each(function(index, elem) {
                         const $listItem = $(elem),
                             newIndex = $listItem.index();

                         $listItem.find(':hidden').val(newIndex);
                    });
                }
            });
            $('#projectId').on('change', function (e) {
                e.preventDefault();
                location.href = location.pathname + '?project_id=' + $(this).val();
            });
        });
    </script>
    <style>
        .draggable {
            cursor: move;
        }
    </style>
@endsection
