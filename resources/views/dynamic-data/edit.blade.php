@extends('adminlte::page')

@section('content')

<div class="alert alert-info">
    dynamicData ID: {{ $dynamicData->id ?? 'not set' }}<br>
    dynamicData Page ID: {{ $dynamicData->dynamic_page_id ?? 'not set' }}
</div>


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit {{ $page ? $page->name : 'Unknown' }} Record</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('dynamic-data.update', $dynamicData) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @foreach($page->fields as $field)
                            <div class="mb-3">
                                <label for="field_{{ $field->name }}" class="form-label">
                                    {{ $field->label }}
                                    @if($field->is_required)<span class="text-danger">*</span>@endif
                                </label>
                                
                                @switch($field->type)
                                    @case('textarea')
                                        <textarea 
                                            class="form-control @error('data.' . $field->name) is-invalid @enderror" 
                                            id="field_{{ $field->name }}" 
                                            name="data[{{ $field->name }}]" 
                                            rows="4"
                                            {{ $field->is_required ? 'required' : '' }}
                                        >{{ old('data.' . $field->name, $dynamicData->data[$field->name] ?? '') }}</textarea>
                                        @break
                                    
                                    @case('select')
                                        <select 
                                            class="form-control @error('data.' . $field->name) is-invalid @enderror" 
                                            id="field_{{ $field->name }}" 
                                            name="data[{{ $field->name }}]"
                                            {{ $field->is_required ? 'required' : '' }}
                                        >
                                            <option value="">Select an option</option>
                                            @foreach($field->options as $option)
                                                <option value="{{ $option }}" {{ old('data.' . $field->name, $dynamicData->data[$field->name] ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                        @break
                                    
                                    @case('checkbox')
                                        <div class="form-check">
                                            <input 
                                                type="checkbox" 
                                                class="form-check-input @error('data.' . $field->name) is-invalid @enderror" 
                                                id="field_{{ $field->name }}" 
                                                name="data[{{ $field->name }}]" 
                                                value="1" 
                                                {{ old('data.' . $field->name, $dynamicData->data[$field->name] ?? '') ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="field_{{ $field->name }}">{{ $field->label }}</label>
                                        </div>
                                        @break
                                    
                                    @case('radio')
                                        @foreach($field->options as $option)
                                            <div class="form-check">
                                                <input 
                                                    type="radio" 
                                                    class="form-check-input @error('data.' . $field->name) is-invalid @enderror" 
                                                    id="field_{{ $field->name }}_{{ $loop->index }}" 
                                                    name="data[{{ $field->name }}]" 
                                                    value="{{ $option }}" 
                                                    {{ old('data.' . $field->name, $dynamicData->data[$field->name] ?? '') == $option ? 'checked' : '' }}
                                                    {{ $field->is_required ? 'required' : '' }}
                                                >
                                                <label class="form-check-label" for="field_{{ $field->name }}_{{ $loop->index }}">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                        @break
                                    
                                    @case('file')
                                        @if(isset($dynamicData->data[$field->name]))
                                            <div class="mb-2">
                                                <a href="{{ asset('storage/' . $dynamicData->data[$field->name]) }}" target="_blank">Current File</a>
                                            </div>
                                        @endif
                                        <input 
                                            type="file" 
                                            class="form-control @error('data.' . $field->name) is-invalid @enderror" 
                                            id="field_{{ $field->name }}" 
                                            name="data[{{ $field->name }}]"
                                        >
                                        @break
                                    
                                    @default
                                        <input 
                                            type="{{ $field->type }}" 
                                            class="form-control @error('data.' . $field->name) is-invalid @enderror" 
                                            id="field_{{ $field->name }}" 
                                            name="data[{{ $field->name }}]" 
                                            value="{{ old('data.' . $field->name, $dynamicData->data[$field->name] ?? '') }}"
                                            {{ $field->is_required ? 'required' : '' }}
                                        >
                                @endswitch
                                
                                @error('data.' . $field->name)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update Record</button>
                            <a href="{{ route('dynamic-data.page', $page) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection