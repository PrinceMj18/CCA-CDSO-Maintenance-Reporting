@extends('layouts.apps')

@section('content')

<h2 class="my-4 text-2xl font-semibold text-gray-700 dark:text-gray-200">
    <!-- Dashboard -->
</h2>

@include('pages.dashboard.' . auth()->user()->role->name)

@endsection