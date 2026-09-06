@extends('admin.layouts.app')

@section('content')
<h4 class="mb-4">Dashboard</h4>

<div class="row">
    <div class="col-md-3">
        <div class="card p-3 mb-3">
            <small class="text-muted">Total News</small>
            <h3>{{ $stats['total_news'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 mb-3">
            <small class="text-muted">Published</small>
            <h3>{{ $stats['published_news'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 mb-3">
            <small class="text-muted">Categories</small>
            <h3>{{ $stats['total_categories'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 mb-3">
            <small class="text-muted">Active Ads</small>
            <h3>{{ $stats['active_ads'] }}</h3>
        </div>
    </div>
</div>
@endsection
