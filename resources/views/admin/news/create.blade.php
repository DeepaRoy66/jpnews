@extends('admin.layouts.app')

@section('content')
<h4>Create News</h4>

<form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Category</label>
        <select name="category_id" class="form-control" required>
            <option value="">-- Select Category --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Excerpt</label>
        <textarea name="excerpt" class="form-control" rows="2">{{ old('excerpt') }}</textarea>
    </div>

    <div class="mb-3">
        <label>Body</label>
        <textarea name="body" class="form-control" rows="8" required>{{ old('body') }}</textarea>
        @error('body') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Featured Image</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>

    <div class="mb-3">
        <label>Published At (optional)</label>
        <input type="datetime-local" name="published_at" class="form-control">
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_published" value="1" class="form-check-input" id="isPublished">
        <label class="form-check-label" for="isPublished">Publish Now</label>
    </div>

    <button class="btn btn-primary">Save</button>
</form>
@endsection
