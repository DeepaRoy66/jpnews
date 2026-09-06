@extends('admin.layouts.app')

@section('content')
<h4>Create Category</h4>

<form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <button class="btn btn-primary">Save</button>
</form>
@endsection
