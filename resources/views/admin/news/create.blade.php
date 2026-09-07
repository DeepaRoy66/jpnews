@extends('admin.layouts.app')

@section('content')
<h4>Add News</h4>

<div class="admin-card">
<form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Category</label>
        <select name="category_id" class="form-control" required>
            <option value="">-- Select --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- ===== Language tabs ===== --}}
    <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="ne-tab" data-bs-toggle="tab" data-bs-target="#ne-pane" type="button" role="tab">
                नेपाली
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-pane" type="button" role="tab">
                English
            </button>
        </li>
    </ul>
    <p class="text-muted small mb-3">कम्तिमा एउटा भाषा (नेपाली वा English) मा title र body भर्नु अनिवार्य छ।</p>

    <div class="tab-content mb-3">
        {{-- Nepali pane --}}
        <div class="tab-pane fade show active" id="ne-pane" role="tabpanel">
            <div class="mb-3">
                <label>Title (नेपाली)</label>
                <input type="text" name="title[ne]" class="form-control" value="{{ old('title.ne') }}">
                @error('title.ne') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3">
                <label>Excerpt (नेपाली)</label>
                <textarea name="excerpt[ne]" class="form-control" rows="2">{{ old('excerpt.ne') }}</textarea>
            </div>
            <div class="mb-3">
                <label>Body (नेपाली)</label>
                <textarea name="body[ne]" class="form-control" rows="8">{{ old('body.ne') }}</textarea>
                @error('body.ne') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- English pane --}}
        <div class="tab-pane fade" id="en-pane" role="tabpanel">
            <div class="mb-3">
                <label>Title (English)</label>
                <input type="text" name="title[en]" class="form-control" value="{{ old('title.en') }}">
            </div>
            <div class="mb-3">
                <label>Excerpt (English)</label>
                <textarea name="excerpt[en]" class="form-control" rows="2">{{ old('excerpt.en') }}</textarea>
            </div>
            <div class="mb-3">
                <label>Body (English)</label>
                <textarea name="body[en]" class="form-control" rows="8">{{ old('body.en') }}</textarea>
            </div>
            <small class="text-muted">English खाली छोड्नुभयो भने, English site मा नेपाली content नै देखिनेछ (fallback)।</small>
        </div>
    </div>
    {{-- ===== End language tabs ===== --}}

    <div class="mb-3">
        <label>Featured Image</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>

    <div class="mb-3">
        <label>Published At</label>
        <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at') }}">
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_published" value="1" class="form-check-input" id="isPublished" {{ old('is_published') ? 'checked' : '' }}>
        <label class="form-check-label" for="isPublished">Published</label>
    </div>

    <button class="btn btn-primary">Create</button>
</form>
</div>

@if($errors->has('title.ne') || $errors->has('body.ne'))
<script>
    // Neither language had enough content — surface the Nepali tab
    // first since it's the primary/default one, so errors are visible.
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Tab(document.getElementById('ne-tab')).show();
    });
</script>
@elseif($errors->has('title.en') || $errors->has('body.en'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Tab(document.getElementById('en-tab')).show();
    });
</script>
@endif
@endsection