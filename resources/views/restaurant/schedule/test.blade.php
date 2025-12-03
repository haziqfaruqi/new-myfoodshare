@extends('restaurant.layouts.restaurant-layout')

@section('title', 'Test Schedule - Restaurant Portal')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Test Schedule</h1>
        <p class="text-sm text-zinc-500 mt-1">Simple test page</p>
    </div>

    <!-- Test Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="text-2xl font-bold text-zinc-900">Test</div>
        </div>
    </div>

    <div class="text-center py-8">
        <p class="text-sm text-zinc-500">If you can see this, the schedule page is working</p>
    </div>
</div>
@endsection