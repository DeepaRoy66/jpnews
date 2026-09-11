@extends('admin.layouts.app')

@section('content')
<h4>Create Ad</h4>

<form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Banner Image</label>
        <input type="file" name="image" class="form-control" accept="image/*" required>
        @error('image') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Link (optional)</label>
        <input type="url" name="link" class="form-control" placeholder="https://...">
    </div>

    <div class="mb-3">
        <label>Position</label>
        <select name="position" class="form-control" required>
            <option value="sidebar">Sidebar</option>
            <option value="footer">Footer</option>
            <option value="between_news">Between News</option>
        </select>
    </div>

    <div class="row">
        <div class="col mb-3">
            <label>Start Date</label>
            <input type="date" name="starts_at" class="form-control">
        </div>
        <div class="col mb-3">
            <label>End Date</label>
            <input type="date" name="ends_at" class="form-control">
        </div>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" checked>
        <label class="form-check-label" for="isActive">Active</label>
    </div>

    <button class="btn btn-primary">Save</button>
</form>
@endsection