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

    <hr class="my-4">

    <div class="mb-3">
        <label class="fw-bold">Layout (देखिने तरिका)</label>
        <select name="layout_type" class="form-control" required>
            @foreach($layoutOptions as $key => $label)
                <option value="{{ $key }}" {{ old('layout_type', $category->layout_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('layout_type') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="fw-bold">Accent Color (रङ)</label>
        <input type="color" name="accent_color" class="form-control form-control-color" value="{{ old('accent_color', $category->accent_color) }}">
        @error('accent_color') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="fw-bold">Icon (Bootstrap Icons class)</label>
        <input type="text" name="icon" class="form-control" value="{{ old('icon', $category->icon) }}">
        <small class="text-muted"><a href="https://icons.getbootstrap.com/" target="_blank">Icon list हेर्न यहाँ क्लिक गर्नुस्</a></small>
        @error('icon') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <button class="btn btn-primary">Update</button>
</form>
</div>
@endsection