@extends('admin.layouts.app')

@section('content')
<h4>Edit Category</h4>

<div class="admin-card">
<form action="{{ route('admin.categories.update', $category) }}" method="POST">
    @csrf @method('PUT')

    <div class="mb-3">
        <label>URL Slug</label>
        <input type="text" class="form-control" value="{{ $category->slug }}" readonly disabled>
    </div>

    <hr class="my-4">
    <p class="text-muted small">कम्तिमा एउटा भाषामा नाम अनिवार्य छ.</p>

    <div class="mb-3">
        <label class="fw-bold">Nepali Name (नेपाली नाम)</label>
        <input type="text" name="name_ne" class="form-control"
               value="{{ old('name_ne', $category->nameIn('ne')) }}">
        @error('name_ne') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="fw-bold">English Name</label>
        <input type="text" name="name_en" class="form-control"
               value="{{ old('name_en', $category->nameIn('en')) }}">
        @error('name_en') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <button class="btn btn-primary">Update</button>
</form>
</div>
@endsection