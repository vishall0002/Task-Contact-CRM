@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')


<div class="content mt-4">
    <h2>Welcome, {{ auth()->user()->name }}</h2>
    <p>You are successfully logged in.</p>
</div>
@endsection

@push('styles')
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #f5f6fa;
    }

   
</style>
@endpush
