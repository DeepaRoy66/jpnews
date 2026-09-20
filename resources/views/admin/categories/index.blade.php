@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Categories</h4>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ New Category</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>Slug</th>
            <th>नेपाली नाम</th>
            <th>English Name</th>
            <th>Navbar</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($categories as $category)
        @php
            $hasNe = (bool) $category->nameIn('ne');
            $hasEn = (bool) $category->nameIn('en');
        @endphp
        <tr>
            <td><code>{{ $category->slug }}</code></td>
            <td>
                @if($hasNe)
                    {{ $category->nameIn('ne') }}
                @else
                    <span class="badge bg-light text-muted border">English only</span>
                @endif
            </td>
            <td>
                @if($hasEn)
                    {{ $category->nameIn('en') }}
                @else
                    <span class="badge bg-light text-muted border">Nepali only</span>
                @endif
            </td>
            <td>
                @if($category->show_in_navbar)
                    <span class="badge bg-success">#{{ $category->nav_order ?? '-' }}</span>
                @else
                    <span class="badge bg-secondary">Off</span>
                @endif
            </td>
            <td>
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="5">कुनै category छैन।</td></tr>
        @endforelse
    </tbody>
</table>

{{ $categories->links() }}
@endsection