@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Field</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('dynamic-fields.update', $dynamicField) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="label" class="form-label">Field Label</label>
                            <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $dynamicField->label) }}" required>
                            <small class="form-text text-muted">This will be displayed to users</small>
                            @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Field Type</label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select a field type</option>
                                @foreach($fieldTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $dynamicField->type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="options-container" style="display: none;">
                            <label for="options" class="form-label">Options</label>
                            <textarea class="form-control @error('options') is-invalid @enderror" id="options" name="options" rows="5" placeholder="Enter each option on a new line">{{ old('options', $optionsString) }}</textarea>
                            <small class="form-text text-muted">Required for select, checkbox, and radio field types</small>
                            @error('options')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">Display Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $dynamicField->order) }}">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('is_required') is-invalid @enderror" id="is_required" name="is_required" value="1" {{ old('is_required', $dynamicField->is_required) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_required">Required Field</label>
                            @error('is_required')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('is_unique') is-invalid @enderror" id="is_unique" name="is_unique" value="1" {{ old('is_unique', $dynamicField->is_unique) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_unique">Unique Value</label>
                            @error('is_unique')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('is_searchable') is-invalid @enderror" id="is_searchable" name="is_searchable" value="1" {{ old('is_searchable', $dynamicField->is_searchable) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_searchable">Searchable</label>
                            @error('is_searchable')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('is_sortable') is-invalid @enderror" id="is_sortable" name="is_sortable" value="1" {{ old('is_sortable', $dynamicField->is_sortable) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_sortable">Sortable</label>
                            @error('is_sortable')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('is_visible') is-invalid @enderror" id="is_visible" name="is_visible" value="1" {{ old('is_visible', $dynamicField->is_visible) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_visible">Visible in Table</label>
                            @error('is_visible')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update Field</button>
                            <a href="{{ route('dynamic-pages.edit', $dynamicField->dynamic_page_id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const optionsContainer = document.getElementById('options-container');
        
        function toggleOptionsField() {
            const selectedType = typeSelect.value;
            if (['select', 'checkbox', 'radio'].includes(selectedType)) {
                optionsContainer.style.display = 'block';
            } else {
                optionsContainer.style.display = 'none';
            }
        }
        
        toggleOptionsField();
        typeSelect.addEventListener('change', toggleOptionsField);
    });
</script>
@endpush
@endsection