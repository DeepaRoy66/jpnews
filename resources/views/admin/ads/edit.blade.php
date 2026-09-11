@extends('admin.layouts.app')

@section('content')
<h4>Edit Ad</h4>

<form action="{{ route('admin.ads.update', $ad) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $ad->title) }}" required>
        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-2">
        <img src="{{ asset('storage/'.$ad->image) }}" width="150">
    </div>
    <div class="mb-3">
        <label>Replace Banner Image</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>

    <div class="mb-3">
        <label>Link (optional)</label>
        <input type="url" name="link" class="form-control" value="{{ old('link', $ad->link) }}">
    </div>

    <div class="mb-3">
        <label>Position</label>
        <select name="position" class="form-control" required>
            @foreach(['sidebar', 'footer', 'between_news'] as $pos)
                <option value="{{ $pos }}" {{ $ad->position == $pos ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $pos)) }}</option>
            @endforeach
        </select>
        @if($ad->position === 'navbar')
            <small class="text-danger">यो ad हाल "Navbar" position मा राखिएको छ, जुन अब frontend मा देखिँदैन। कृपया माथिबाट अर्को position छान्नुहोस्।</small>
        @endif
    </div>

    <div class="row">
        <div class="col mb-3">
            <label>Start Date</label>
            <input type="date" name="starts_at" class="form-control" value="{{ $ad->starts_at?->format('Y-m-d') }}">
        </div>
        <div class="col mb-3">
            <label>End Date</label>
            <input type="date" name="ends_at" class="form-control" value="{{ $ad->ends_at?->format('Y-m-d') }}">
        </div>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" {{ $ad->is_active ? 'checked' : '' }}>
        <label class="form-check-label" for="isActive">Active</label>
    </div>

    <button class="btn btn-primary">Update</button>
</form>
@endsection