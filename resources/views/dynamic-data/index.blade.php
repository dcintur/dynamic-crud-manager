@extends('adminlte::page')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        @if($page->icon)
                            <i class="{{ $page->icon }}"></i>
                        @else
                            <i class="bi bi-table"></i>
                        @endif
                        {{ $page->name }} - Records
                    </h5>
                    <div>
                        <a href="{{ route('dynamic-data.create', $page) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg"></i> Add New Record
                        </a>
                        <a href="{{ route('dynamic-pages.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Pages
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

<!-- Advanced Filter Section -->
<div class="mb-4">
    <button class="btn btn-outline-secondary mb-2" type="button" data-toggle="collapse" data-target="#filterCollapse">
        <i class="bi bi-funnel"></i> Advanced Filters
    </button>
    
    <div class="collapse" id="filterCollapse">
        <x-advanced-filter :page="$page" />
    </div>
</div>

<!-- Export Section -->
<div class="mb-3 d-flex justify-content-end">
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="bi bi-download"></i> Export
        </button>
        <div class="dropdown-menu" aria-labelledby="exportDropdown">
            <a class="dropdown-item" href="{{ route('dynamic-data.export', ['page' => $page->id, 'format' => 'csv']) }}">
                <i class="bi bi-filetype-csv"></i> CSV
            </a>
            <a class="dropdown-item" href="{{ route('dynamic-data.export', ['page' => $page->id, 'format' => 'xlsx']) }}">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a class="dropdown-item" href="{{ route('dynamic-data.export', ['page' => $page->id, 'format' => 'pdf']) }}">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
    </div>
    
    <button type="button" class="btn btn-outline-primary ml-2" data-toggle="modal" data-target="#importModal">
        <i class="bi bi-upload"></i> Import
    </button>
</div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">#</th>
                                    @foreach($page->fields as $field)
                                        @if($field->is_visible)
                                            <th>
                                                {{ $field->label }}
                                                @if($field->is_sortable)
                                                    <a href="{{ route('dynamic-data.page', ['page' => $page->id, 'sort' => $field->name, 'direction' => request('direction') == 'asc' && request('sort') == $field->name ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                                        <i class="bi bi-arrow-{{ request('direction') == 'asc' && request('sort') == $field->name ? 'up' : 'down' }} text-muted"></i>
                                                    </a>
                                                @endif
                                            </th>
                                        @endif
                                    @endforeach
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        @foreach($page->fields as $field)
                                            @if($field->is_visible)
                                                <td>
                                                    @if(isset($item->data[$field->name]))
                                                        @switch($field->type)
                                                            @case('file')
                                                                <a href="{{ asset('storage/' . $item->data[$field->name]) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                                    <i class="bi bi-file-earmark"></i> View File
                                                                </a>
                                                                @break
                                                            @case('checkbox')
                                                                <span class="badge bg-{{ $item->data[$field->name] ? 'success' : 'secondary' }}">
                                                                    {{ $item->data[$field->name] ? 'Yes' : 'No' }}
                                                                </span>
                                                                @break
                                                            @case('date')
                                                                {{ \Carbon\Carbon::parse($item->data[$field->name])->format('Y-m-d') }}
                                                                @break
                                                            @default
                                                                {{ $item->data[$field->name] }}
                                                        @endswitch
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('dynamic-data.edit', $item) }}" class="btn btn-info btn-sm">
                                                    Edit
                                                </a>
                                                <form action="{{ route('dynamic-data.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $page->fields->where('is_visible', true)->count() + 2 }}" class="text-center py-4">
                                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                            <p class="mt-2 mb-0 text-muted">No records found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('dynamic-data.import', $page) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="importFile">CSV/Excel File</label>
                        <input type="file" class="form-control-file" id="importFile" name="importFile" accept=".csv,.xlsx,.xls" required>
                        <small class="form-text text-muted">File must be in CSV or Excel format</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> <strong>Note:</strong> The first row should contain column headers that match the field labels.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection