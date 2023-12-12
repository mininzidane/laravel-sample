@extends('layout')

@section('content')
    <h2 class="h-2">Create task</h2>
    @include('task/_form', ['projects' => $projects, 'task' => null])
@endsection
