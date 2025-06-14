@extends('adminlte::page')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create Dynamic Page</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('dynamic-pages.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Page Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">Icon</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i id="icon-preview" class="{{ old('icon', 'bi bi-grid') }}"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="collapse" data-target="#icon-selector" aria-expanded="false" aria-controls="icon-selector">
                                        Select Icon
                                    </button>
                                </div>
                            </div>
                            <div class="form-text">Choose an icon from Bootstrap Icons</div>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="mt-2 collapse" id="icon-selector">
                                <div class="card card-body">
                                    <div class="row">
                                        @foreach(['bi-grid', 'bi-list', 'bi-table', 'bi-people', 'bi-person', 'bi-building', 'bi-house', 'bi-cart', 'bi-bag', 'bi-credit-card', 'bi-calendar', 'bi-clock', 'bi-clipboard', 'bi-file-text', 'bi-folder', 'bi-envelope', 'bi-telephone', 'bi-chat', 'bi-camera', 'bi-image', 'bi-music-note', 'bi-film', 'bi-map', 'bi-truck', 'bi-car-front', 'bi-airplane', 'bi-laptop', 'bi-phone', 'bi-printer', 'bi-tools'] as $icon)
                                            <div class="col-2 text-center mb-2">
                                                <button type="button" class="btn btn-outline-secondary btn-icon-select" data-icon="bi {{ $icon }}">
                                                    <i class="bi {{ $icon }}"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="menu_group" class="form-label">Menu Group</label>
                            <input type="text" class="form-control @error('menu_group') is-invalid @enderror" id="menu_group" name="menu_group" value="{{ old('menu_group') }}">
                            <div class="form-text">Group pages in the menu (e.g. "Admin", "Reports", etc.)</div>
                            @error('menu_group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">Display Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}">
                            <div class="form-text">Determines the order in the menu (lower numbers appear first)</div>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                            <div class="form-text">Inactive pages won't appear in the menu</div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Create Page
                            </button>
                            <a href="{{ route('dynamic-pages.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        const iconInput = $('#icon');
        const iconPreview = $('#icon-preview');
        
        // Update preview when input changes
        iconInput.on('input', function() {
            iconPreview.attr('class', $(this).val() || 'bi bi-grid');
        });
        
        // Handle icon selection
        $('.btn-icon-select').on('click', function() {
            const icon = $(this).data('icon');
            iconInput.val(icon);
            iconPreview.attr('class', icon);
            $('#icon-selector').collapse('hide');
        });
    });
</script>
@endpush
@endsection