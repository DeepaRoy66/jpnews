@extends('admin.layouts.app')

@section('content')
<h4>Create Category</h4>

<div class="admin-card">
<form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>URL Slug (English, e.g. "politics")</label>
        <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="politics" required>
        @error('slug') <small class="text-danger d-block">{{ $message }}</small> @enderror
    </div>

    <hr class="my-4">
    <p class="text-muted small">कम्तिमा एउटा भाषामा नाम अनिवार्य छ. दुबै भर्नु पर्दैन — बाँकी भाषा पछि पनि थप्न सकिन्छ.</p>

    <div class="mb-3">
        <label class="fw-bold">Nepali Name (नेपाली नाम)</label>
        <input type="text" name="name_ne" class="form-control" value="{{ old('name_ne') }}" placeholder="जस्तै: राजनीति">
        @error('name_ne') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="fw-bold">English Name</label>
        <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}" placeholder="e.g. Politics">
        @error('name_en') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <button class="btn btn-primary">Save</button>
</form>
</div>
@endsection