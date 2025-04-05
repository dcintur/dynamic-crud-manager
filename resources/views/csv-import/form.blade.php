@extends('adminlte::page')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create Page from CSV</h5>
                </div>

                <div class="card-body">
                    <p class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Upload a CSV file to automatically create a new dynamic page with fields matching your CSV structure.
                    </p>

                    <form method="POST" action="{{ route('csv-import.process') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="page_name" class="form-label">Page Name</label>
                            <input type="text" class="form-control @error('page_name') is-invalid @enderror" id="page_name" name="page_name" value="{{ old('page_name') }}" required>
                            <div class="form-text">This will be the name of your new page</div>
                            @error('page_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="menu_group" class="form-label">Menu Group (Optional)</label>
                            <input type="text" class="form-control @error('menu_group') is-invalid @enderror" id="menu_group" name="menu_group" value="{{ old('menu_group') }}">
                            <div class="form-text">Group this page with others in the menu</div>
                            @error('menu_group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="csv_file" class="form-label">CSV File</label>
                            <input type="file" class="form-control @error('csv_file') is-invalid @enderror" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                            <div class="form-text">Upload a CSV file</div>
                            @error('csv_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('has_header') is-invalid @enderror" id="has_header" name="has_header" value="1" {{ old('has_header', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_header">File has header row</label>
                            <div class="form-text">First row contains column names</div>
                            @error('has_header')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Import and Create Page
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
@endsection