@extends('adminlte::page')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Dynamic Page</h5>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('dynamic-pages.update', $page) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Page Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $page->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">Icon</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i id="icon-preview" class="{{ old('icon', $page->icon ?? 'bi bi-grid') }}"></i>
                                </span>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $page->icon) }}">
                                <button class="btn btn-outline-secondary" type="button" id="icon-selector-btn">Select Icon</button>
                            </div>
                            <div class="form-text">Choose an icon from Bootstrap Icons</div>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="mt-2 collapse" id="icon-selector">
                                <div class="card card-body">
                                    <div class="row row-cols-6 g-2">
                                        @foreach(['bi-grid', 'bi-list', 'bi-table', 'bi-people', 'bi-person', 'bi-building', 'bi-house', 'bi-cart', 'bi-bag', 'bi-credit-card', 'bi-calendar', 'bi-clock', 'bi-clipboard', 'bi-file-text', 'bi-folder', 'bi-envelope', 'bi-telephone', 'bi-chat', 'bi-camera', 'bi-image', 'bi-music-note', 'bi-film', 'bi-map', 'bi-truck', 'bi-car-front', 'bi-airplane', 'bi-laptop', 'bi-phone', 'bi-printer', 'bi-tools'] as $icon)
                                            <div class="col text-center">
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
                            <input type="text" class="form-control @error('menu_group') is-invalid @enderror" id="menu_group" name="menu_group" value="{{ old('menu_group', $page->menu_group) }}">
                            <div class="form-text">Group pages in the menu (e.g. "Admin", "Reports", etc.)</div>
                            @error('menu_group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">Display Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $page->order) }}">
                            <div class="form-text">Determines the order in the menu (lower numbers appear first)</div>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" id="is_active" name="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                            <div class="form-text">Inactive pages won't appear in the menu</div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Page
                            </button>
                            <a href="{{ route('dynamic-pages.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Fields Management -->
            <div class="card mt-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Fields</h5>
                    <a href="{{ route('dynamic-fields.create', ['page_id' => $page->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Field
                    </a>
                </div>

                <div class="card-body">
                    @if ($page->fields->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Label</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($page->fields->sortBy('order') as $field)
                                    <tr>
                                        <td>{{ $field->label }}</td>
                                        <td><code>{{ $field->name }}</code></td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $field->type }}</span>
                                        </td>
                                        <td>
                                            @if($field->is_required)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-light text-dark">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $field->order }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('dynamic-fields.edit', $field) }}" class="btn btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('dynamic-fields.destroy', $field) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this field?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No fields added yet. Click the "Add Field" button to create your first field.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Records Link -->
            <div class="card mt-4 shadow-sm">
                <div class="card-body text-center">
                    <a href="{{ route('dynamic-data.page', $page) }}" class="btn btn-success">
                        <i class="bi bi-table"></i> View Data Records
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const iconInput = document.getElementById('icon');
        const iconPreview = document.getElementById('icon-preview');
        const iconSelectorBtn = document.getElementById('icon-selector-btn');
        const iconSelector = document.getElementById('icon-selector');
        
        // Update preview when input changes
        iconInput.addEventListener('input', function() {
            iconPreview.className = this.value || 'bi bi-grid';
        });
        
        // Toggle icon selector
        iconSelectorBtn.addEventListener('click', function() {
            iconSelector.classList.toggle('show');
        });
        
        // Handle icon selection
        document.querySelectorAll('.btn-icon-select').forEach(button => {
            button.addEventListener('click', function() {
                const icon = this.getAttribute('data-icon');
                iconInput.value = icon;
                iconPreview.className = icon;
                iconSelector.classList.remove('show');
            });
        });
    });
</script>
@endpush
@endsection