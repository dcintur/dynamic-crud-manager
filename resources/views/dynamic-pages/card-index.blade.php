@extends('layouts.app')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Dynamic Pages</h1>
        <div>
            <a href="{{ route('csv-import.form') }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-spreadsheet"></i> Create from CSV
            </a>
            <a href="{{ route('dynamic-pages.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Create New Page
            </a>
        </div>
    </div>
    
    <div class="row">
        @foreach($pages as $page)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            @if($page->icon)
                                <i class="{{ $page->icon }}"></i>
                            @else
                                <i class="bi bi-grid-1x2"></i>
                            @endif
                            {{ $page->name }}
                        </h5>
                        <span class="badge {{ $page->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $page->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Menu Group:</strong> {{ $page->menu_group ?: 'Main' }}<br>
                            <strong>Fields:</strong> {{ $page->fields->count() }}<br>
                            <strong>Records:</strong> {{ $page->data->count() }}
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100">
                            <a href="{{ route('dynamic-data.page', $page) }}" class="btn btn-outline-primary">
                                <i class="bi bi-table"></i> Records
                            </a>
                            <a href="{{ route('dynamic-pages.edit', $page) }}" class="btn btn-outline-info">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('dynamic-pages.show', $page) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </div>
                        <form action="{{ route('dynamic-pages.destroy', $page) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this page?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection