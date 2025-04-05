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
                                    <select name="filters[{{ $index }}][join]" class="form-control form-control-sm" style="width: 100px;">
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
                                    <select name="filters[{{ $index }}][field]" class="form-control filter-field">
                                        <option value="">Select Field</option>
                                        @foreach($page->fields as $field)
                                            @if($field->is_searchable)
                                                <option value="{{ $field->name }}" {{ ($filter['field'] ?? '') == $field->name ? 'selected' : '' }}>{{ $field->label }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="filters[{{ $index }}][operator]" class="form-control">
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
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-primary" id="add-filter">
                                                    <i class="bi bi-plus-lg"></i>
                                                </button>
                                            </div>
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
                                <select name="filters[0][field]" class="form-control filter-field">
                                    <option value="">Select Field</option>
                                    @foreach($page->fields as $field)
                                        @if($field->is_searchable)
                                            <option value="{{ $field->name }}">{{ $field->label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="filters[0][operator]" class="form-control">
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
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-primary" id="add-filter">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('dynamic-data.page', $page) }}" class="btn btn-secondary ml-2">Clear</a>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        // Gestisci il pulsante Add Filter
        $('#add-filter').on('click', function() {
            const filterCount = $('.filter-group').length;
            const newFilter = `
                <div class="filter-group mb-3">
                    <div class="d-flex mb-2">
                        <select name="filters[${filterCount}][join]" class="form-control form-control-sm" style="width: 100px;">
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
                            <select name="filters[${filterCount}][field]" class="form-control filter-field">
                                <option value="">Select Field</option>
                                ${$('.filter-field:first option').map(function() {
                                    return '<option value="' + $(this).val() + '">' + $(this).text() + '</option>';
                                }).get().join('')}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="filters[${filterCount}][operator]" class="form-control">
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
                </div>
            `;
            $('#filter-container').append(newFilter);
            
            // Aggiungi event listener al nuovo pulsante di rimozione
            $('.remove-filter[data-index="' + filterCount + '"]').on('click', handleRemoveFilter);
        });
        
        // Event listener per i pulsanti di rimozione esistenti
        $('.remove-filter').on('click', handleRemoveFilter);
        
        function handleRemoveFilter() {
            $(this).closest('.filter-group').remove();
            
            // Reindicizza i filtri rimanenti
            $('.filter-group').each(function(index) {
                const $group = $(this);
                $group.find('select[name*="[join]"]').attr('name', 'filters[' + index + '][join]');
                $group.find('select[name*="[field]"]').attr('name', 'filters[' + index + '][field]');
                $group.find('select[name*="[operator]"]').attr('name', 'filters[' + index + '][operator]');
                $group.find('input[name*="[value]"]').attr('name', 'filters[' + index + '][value]');
                $group.find('.remove-filter').attr('data-index', index);
            });
        }
    });
</script>
@endpush