<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Advanced Filter</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('dynamic-data.page', $page) }}" method="GET" id="advancedFilterForm">
            <div id="filter-container">
                @if (count($filters) > 0)
                    @foreach ($filters as $index => $filter)
                        <div class="filter-group mb-3">
                            @if ($index > 0)
                                <div class="d-flex mb-2">
                                    <select name="filters[{{ $index }}][join]" class="form-select form-select-sm" style="width: 100px;">
                                        <option value="and" {{ ($filter['join'] ?? 'and') == 'and' ? 'selected' : '' }}>AND</option>
                                        <option value="or" {{ ($filter['join'] ?? 'and') == 'or' ? 'selected' : '' }}>OR</option>
                                    </select>
                                    <div class="flex-grow-1"></div>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-filter" data-index="{{ $index }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-4">
                                    <select name="filters[{{ $index }}][field]" class="form-select filter-field">
                                        <option value="">Select Field</option>
                                        @foreach($page->fields as $field)
                                            @if($field->is_searchable)
                                                <option value="{{ $field->name }}" {{ ($filter['field'] ?? '') == $field->name ? 'selected' : '' }}>{{ $field->label }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="filters[{{ $index }}][operator]" class="form-select">
                                        <option value="equals" {{ ($filter['operator'] ?? '') == 'equals' ? 'selected' : '' }}>Equals</option>
                                        <option value="contains" {{ ($filter['operator'] ?? '') == 'contains' ? 'selected' : '' }}>Contains</option>
                                        <option value="starts_with" {{ ($filter['operator'] ?? '') == 'starts_with' ? 'selected' : '' }}>Starts With</option>
                                        <option value="ends_with" {{ ($filter['operator'] ?? '') == 'ends_with' ? 'selected' : '' }}>Ends With</option>
                                        <option value="greater_than" {{ ($filter['operator'] ?? '') == 'greater_than' ? 'selected' : '' }}>Greater Than</option>
                                        <option value="less_than" {{ ($filter['operator'] ?? '') == 'less_than' ? 'selected' : '' }}>Less Than</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="filters[{{ $index }}][value]" value="{{ $filter['value'] ?? '' }}">
                                        @if ($index === 0)
                                            <button type="button" class="btn btn-outline-primary" id="add-filter">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="filter-group mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="filters[0][field]" class="form-select filter-field">
                                    <option value="">Select Field</option>
                                    @foreach($page->fields as $field)
                                        @if($field->is_searchable)
                                            <option value="{{ $field->name }}">{{ $field->label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="filters[0][operator]" class="form-select">
                                    <option value="equals">Equals</option>
                                    <option value="contains">Contains</option>
                                    <option value="starts_with">Starts With</option>
                                    <option value="ends_with">Ends With</option>
                                    <option value="greater_than">Greater Than</option>
                                    <option value="less_than">Less Than</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="filters[0][value]">
                                    <button type="button" class="btn btn-outline-primary" id="add-filter">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('dynamic-data.page', $page) }}" class="btn btn-secondary ms-2">Clear</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterContainer = document.getElementById('filter-container');
        const addFilterBtn = document.getElementById('add-filter');

        if (addFilterBtn) {
            addFilterBtn.addEventListener('click', function() {
                const filterCount = document.querySelectorAll('.filter-group').length;
                const newFilter = document.createElement('div');
                newFilter.className = 'filter-group mb-3';
                newFilter.innerHTML = `
                    <div class="d-flex mb-2">
                        <select name="filters[${filterCount}][join]" class="form-select form-select-sm" style="width: 100px;">
                            <option value="and">AND</option>
                            <option value="or">OR</option>
                        </select>
                        <div class="flex-grow-1"></div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-filter" data-index="${filterCount}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <select name="filters[${filterCount}][field]" class="form-select filter-field">
                                <option value="">Select Field</option>
                                ${Array.from(document.querySelector('.filter-field').options).map(option => 
                                    `<option value="${option.value}">${option.text}</option>`
                                ).join('')}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="filters[${filterCount}][operator]" class="form-select">
                                <option value="equals">Equals</option>
                                <option value="contains">Contains</option>
                                <option value="starts_with">Starts With</option>
                                <option value="ends_with">Ends With</option>
                                <option value="greater_than">Greater Than</option>
                                <option value="less_than">Less Than</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="filters[${filterCount}][value]">
                        </div>
                    </div>
                `;
                filterContainer.appendChild(newFilter);
                
                // Add event listener to the new remove button
                newFilter.querySelector('.remove-filter').addEventListener('click', handleRemoveFilter);
            });
        }

        // Add event listeners to existing remove buttons
        document.querySelectorAll('.remove-filter').forEach(button => {
            button.addEventListener('click', handleRemoveFilter);
        });

        function handleRemoveFilter(e) {
            const filterGroup = e.target.closest('.filter-group');
            filterGroup.remove();
            
            // Reindex the remaining filters
            const filterGroups = document.querySelectorAll('.filter-group');
            filterGroups.forEach((group, index) => {
                const joinSelect = group.querySelector('select[name*="[join]"]');
                const fieldSelect = group.querySelector('select[name*="[field]"]');
                const operatorSelect = group.querySelector('select[name*="[operator]"]');
                const valueInput = group.querySelector('input[name*="[value]"]');
                const removeBtn = group.querySelector('.remove-filter');
                
                if (joinSelect) {
                    joinSelect.name = `filters[${index}][join]`;
                }
                
                if (fieldSelect) {
                    fieldSelect.name = `filters[${index}][field]`;
                }
                
                if (operatorSelect) {
                    operatorSelect.name = `filters[${index}][operator]`;
                }
                
                if (valueInput) {
                    valueInput.name = `filters[${index}][value]`;
                }
                
                if (removeBtn) {
                    removeBtn.dataset.index = index;
                }
            });
        }
    });
</script>
@endpush