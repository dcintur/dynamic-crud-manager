@extends('adminlte::page')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $page->name }} - Details</span>
                    <div>
                        <a href="{{ route('dynamic-data.page', $page) }}" class="btn btn-sm btn-primary">View Records</a>
                        <a href="{{ route('dynamic-pages.edit', $page) }}" class="btn btn-sm btn-info">Edit Page</a>
                        <a href="{{ route('dynamic-pages.index') }}" class="btn btn-sm btn-secondary">Back to Pages</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5>Page Details</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px;">Name</th>
                                <td>{{ $page->name }}</td>
                            </tr>
                            <tr>
                                <th>Slug</th>
                                <td>{{ $page->slug }}</td>
                            </tr>
                            <tr>
                                <th>Menu Group</th>
                                <td>{{ $page->menu_group ?: 'Main' }}</td>
                            </tr>
                            <tr>
                                <th>Icon</th>
                                <td>
                                    @if($page->icon)
                                        <i class="{{ $page->icon }}"></i> ({{ $page->icon }})
                                    @else
                                        No icon
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Display Order</th>
                                <td>{{ $page->order }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($page->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created</th>
                                <td>{{ $page->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td>{{ $page->updated_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div>
                        <h5>Fields ({{ $page->fields->count() }})</h5>
                        @if($page->fields->count() > 0)
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Label</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Visible</th>
                                        <th>Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($page->fields->sortBy('order') as $field)
                                    <tr>
                                        <td>{{ $field->label }}</td>
                                        <td><code>{{ $field->name }}</code></td>
                                        <td>{{ $field->type }}</td>
                                        <td>{{ $field->is_required ? 'Yes' : 'No' }}</td>
                                        <td>{{ $field->is_visible ? 'Yes' : 'No' }}</td>
                                        <td>{{ $field->order }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info">
                                No fields have been added to this page yet.
                                <a href="{{ route('dynamic-fields.create', ['page_id' => $page->id]) }}" class="alert-link">Add your first field</a>.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection