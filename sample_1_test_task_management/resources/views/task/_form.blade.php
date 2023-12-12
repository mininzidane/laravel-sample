<?php
/**
 * @var \App\Models\Task $task
 */
?>
<form action="{{Request::url()}}" method="POST">
    @csrf

    <label for="project_id" class="form-label">Project</label>
    <select id="project_id" class="form-control mb-4" name="project_id">
        @foreach($projects as $id => $name)
            <option value="{{$id}}" {{$task?->project_id === $id ? 'selected' : ''}}>{{$name}}</option>
        @endforeach
    </select>
    <label for="name" class="form-label">Name</label>
    <input required id="name" class="form-control mb-4" type="text" name="name" value="{{$task?->name}}">
    <label for="priority" class="form-label">Priority</label>
    <input id="priority" class="form-control mb-4" type="number" name="priority" value="{{$task?->priority}}">
    <button class="btn btn-primary" type="submit">Save</button>
    <a href="{{route('task_list')}}" class="btn btn-success">Task list</a>
</form>
