@extends('admin.layouts.app')

@section('content')
<h4>Create News</h4>

<form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>भाषा / Language</label>
        <div>
            <label class="me-3"><input type="radio" name="locale" id="localeNe" value="ne" {{ old('locale', 'ne') == 'ne' ? 'checked' : '' }}> नेपाली</label>
            <label><input type="radio" name="locale" id="localeEn" value="en" {{ old('locale') == 'en' ? 'checked' : '' }}> English</label>
        </div>
        @error('locale') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Category</label>
        <select name="category_id" id="categorySelect" class="form-control" required>
            <option value="">-- Select Category --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                        data-ne="{{ $category->nameIn('ne') ? '1' : '0' }}"
                        data-en="{{ $category->nameIn('en') ? '1' : '0' }}"
                        data-name-ne="{{ $category->nameIn('ne') }}"
                        data-name-en="{{ $category->nameIn('en') }}"
                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->nameIn('ne') ?? $category->nameIn('en') }} ({{ $category->slug }})
                </option>
            @endforeach
        </select>
        <small class="text-muted">छानिएको भाषा अनुसार, त्यही भाषामा नाम भएका category मात्र देखिन्छन्।</small>
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

<script>
    (function () {
        const localeNe = document.getElementById('localeNe');
        const localeEn = document.getElementById('localeEn');
        const select = document.getElementById('categorySelect');
        const options = Array.from(select.options);

        function filterCategories() {
            const locale = localeEn.checked ? 'en' : 'ne';
            let firstVisibleSet = false;

            options.forEach(function (opt) {
                if (opt.value === '') {
                    opt.hidden = false;
                    return;
                }

                const available = opt.dataset[locale] === '1';
                opt.hidden = !available;

                if (available) {
                    const label = locale === 'ne' ? opt.dataset.nameNe : opt.dataset.nameEn;
                    opt.textContent = label;
                }

                if (available && opt.selected) {
                    firstVisibleSet = true;
                }
            });

            // If the currently selected category isn't available in the
            // newly chosen language, reset the selection back to placeholder.
            if (!firstVisibleSet && select.value !== '') {
                select.value = '';
            }
        }

        localeNe.addEventListener('change', filterCategories);
        localeEn.addEventListener('change', filterCategories);
        filterCategories();
    })();
</script>
@endsection