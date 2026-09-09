@extends('admin.layouts.app')

@section('content')
<h4>Edit News</h4>

<form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="mb-3">
        <label>भाषा / Language</label>
        <div>
            <label class="me-3"><input type="radio" name="locale" value="ne" {{ old('locale', $news->locale) == 'ne' ? 'checked' : '' }}> नेपाली</label>
            <label><input type="radio" name="locale" value="en" {{ old('locale', $news->locale) == 'en' ? 'checked' : '' }}> English</label>
        </div>
        @error('locale') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Category</label>
        <select name="category_id" class="form-control" required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $news->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->nameIn('ne') ?? $category->nameIn('en') }} ({{ $category->slug }})
                </option>
            @endforeach
        </select>
        @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $news->title) }}" required>
        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Excerpt</label>
        <textarea name="excerpt" class="form-control" rows="2">{{ old('excerpt', $news->excerpt) }}</textarea>
    </div>

    <div class="mb-3">
        <label>Body</label>
        <textarea name="body" class="form-control" rows="8" required>{{ old('body', $news->body) }}</textarea>
        @error('body') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    @if($news->image)
        <div class="mb-2">
            <img src="{{ asset('storage/'.$news->image) }}" width="120">
        </div>
    @endif

    <div class="mb-3">
        <label>Replace Featured Image</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>

    <div class="mb-3">
        <label>Published At</label>
        <input type="datetime-local" name="published_at" class="form-control"
               value="{{ $news->published_at?->format('Y-m-d\TH:i') }}">
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_published" value="1" class="form-check-input" id="isPublished" {{ $news->is_published ? 'checked' : '' }}>
        <label class="form-check-label" for="isPublished">Published</label>
    </div>

    <button class="btn btn-primary">Update</button>
</form>
@endsection