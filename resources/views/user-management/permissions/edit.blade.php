@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Permissions for {{ $role->name }}</span>
                    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary">Back to Roles</a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('roles.permissions.update', $role) }}">
                        @csrf

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Page</th>
                                    <th>View</th>
                                    <th>Create</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                    <th>Export</th>
                                    <th>Import</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pages as $page)
                                    <tr>
                                        <td>{{ $page->name }}</td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $page->id }}][view]" value="1" 
                                                {{ isset($permissions[$page->id]) && $permissions[$page->id]->can_view ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $page->id }}][create]" value="1" 
                                                {{ isset($permissions[$page->id]) && $permissions[$page->id]->can_create ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $page->id }}][edit]" value="1" 
                                                {{ isset($permissions[$page->id]) && $permissions[$page->id]->can_edit ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $page->id }}][delete]" value="1" 
                                                {{ isset($permissions[$page->id]) && $permissions[$page->id]->can_delete ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $page->id }}][export]" value="1" 
                                                {{ isset($permissions[$page->id]) && $permissions[$page->id]->can_export ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $page->id }}][import]" value="1" 
                                                {{ isset($permissions[$page->id]) && $permissions[$page->id]->can_import ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mb-3 mt-3">
                            <button type="submit" class="btn btn-primary">Save Permissions</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection