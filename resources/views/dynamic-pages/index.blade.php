@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Dynamic Pages</span>
                    <a href="{{ route('dynamic-pages.create') }}" class="btn btn-sm btn-primary">Create New Page</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Menu Group</th>
                                <th>Status</th>
                                <th>Fields</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pages as $page)
                            <tr>
                                <td>
                                    @if ($page->icon)
                                        <i class="{{ $page->icon }}"></i>
                                    @endif
                                    {{ $page->name }}
                                </td>
                                <td>{{ $page->slug }}</td>
                                <td>{{ $page->menu_group }}</td>
                                <td>
                                    @if ($page->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $page->fields->count() }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('dynamic-pages.edit', $page) }}" class="btn btn-sm btn-info">Edit</a>
                                        <a href="{{ route('dynamic-pages.show', $page) }}" class="btn btn-sm btn-primary">View</a>
                                        <form action="{{ route('dynamic-pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this page?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection