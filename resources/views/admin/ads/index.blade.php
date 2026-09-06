@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Ads Management</h4>
    <a href="{{ route('admin.ads.create') }}" class="btn btn-primary">+ New Ad</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Preview</th>
            <th>Title</th>
            <th>Position</th>
            <th>Status</th>
            <th>Runs</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ads as $ad)
        <tr>
            <td><img src="{{ asset('storage/'.$ad->image) }}" width="80"></td>
            <td>{{ $ad->title }}</td>
            <td><span class="badge bg-info">{{ $ad->position }}</span></td>
            <td>
                @if($ad->is_active)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Inactive</span>
                @endif
            </td>
            <td>{{ $ad->starts_at?->format('Y-m-d') ?? '—' }} to {{ $ad->ends_at?->format('Y-m-d') ?? '—' }}</td>
            <td>
                <a href="{{ route('admin.ads.edit', $ad) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this ad?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $ads->links() }}
@endsection
