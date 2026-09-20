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
    <p class="text-muted small">कम्तिमा एउटा भाषामा नाम अनिवार्य छ.</p>

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

    <hr class="my-4">
    <p class="text-muted small">यो category कसरी देखियोस् भन्ने रोज्नुस्.</p>

    <div class="mb-3">
        <label class="fw-bold">Layout (देखिने तरिका)</label>
        <select name="layout_type" class="form-control" required>
            @foreach($layoutOptions as $key => $label)
                <option value="{{ $key }}" {{ old('layout_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('layout_type') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="fw-bold">Accent Color (रङ)</label>
        <input type="color" name="accent_color" class="form-control form-control-color" value="{{ old('accent_color', '#e30613') }}">
        @error('accent_color') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="fw-bold">Icon (Bootstrap Icons class)</label>
        <input type="text" name="icon" class="form-control" value="{{ old('icon', 'bi-newspaper') }}" placeholder="bi-newspaper">
        <small class="text-muted">
            <a href="https://icons.getbootstrap.com/" target="_blank">Icon list हेर्न यहाँ क्लिक गर्नुस्</a> —
            उदाहरण: bi-bank, bi-trophy, bi-film, bi-cpu, bi-globe, bi-heart-pulse
        </small>
        @error('icon') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <hr class="my-4">
    <p class="text-muted small">Navbar मा कुन भाषामा देखाउने भनेर छुट्टाछुट्टै छान्नुहोस् (बढीमा ६ वटा प्रत्येक भाषामा).</p>

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="show_in_navbar_ne" name="show_in_navbar_ne" value="1"
                       {{ old('show_in_navbar_ne') ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="show_in_navbar_ne">नेपाली Navbar मा देखाउने?</label>
            </div>
            @error('show_in_navbar_ne') <small class="text-danger d-block">{{ $message }}</small> @enderror
        </div>
        <div class="col-md-6">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="show_in_navbar_en" name="show_in_navbar_en" value="1"
                       {{ old('show_in_navbar_en') ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="show_in_navbar_en">English Navbar मा देखाउने?</label>
            </div>
            @error('show_in_navbar_en') <small class="text-danger d-block">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="mb-3">
        <label class="fw-bold">Navbar Order (१ = सबैभन्दा पहिले)</label>
        <input type="number" name="nav_order" class="form-control" min="1" max="6"
               value="{{ old('nav_order') }}" placeholder="1 देखि 6 सम्म">
        <small class="text-muted">यो order दुवै भाषाको navbar मा एउटै हुन्छ. कुनैमा टिक नगरे यो field ले काम गर्दैन.</small>
        @error('nav_order') <small class="text-danger d-block">{{ $message }}</small> @enderror
    </div>

    <button class="btn btn-primary">Save</button>
</form>
</div>
@endsection