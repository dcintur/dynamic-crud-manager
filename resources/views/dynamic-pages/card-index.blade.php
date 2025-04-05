@extends('adminlte::page')

@section('title', 'Gestione Pagine Dinamiche')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Gestione Pagine Dinamiche</h1>
        <div>
            <a href="{{ route('csv-import.form') }}" class="btn btn-success">
                <i class="fas fa-file-csv"></i> Crea da CSV
            </a>
            <a href="{{ route('dynamic-pages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Crea Nuova Pagina
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        @foreach($pages as $page)
            <div class="col-md-4 mb-4">
                <div class="card card-outline {{ $page->is_active ? 'card-success' : 'card-secondary' }}">
                    <div class="card-header">
                        <h3 class="card-title">
                            @if($page->icon)
                                <i class="{{ $page->icon }}"></i>
                            @else
                                <i class="fas fa-grip-horizontal"></i>
                            @endif
                            {{ $page->name }}
                        </h3>
                        <div class="card-tools">
                            <span class="badge {{ $page->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $page->is_active ? 'Attivo' : 'Inattivo' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Gruppo Menu:</strong> {{ $page->menu_group ?: 'Principale' }}<br>
                            <strong>Campi:</strong> {{ $page->fields->count() }}<br>
                            <strong>Records:</strong> {{ $page->data->count() }}
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100">
                            <a href="{{ route('dynamic-data.page', $page) }}" class="btn btn-outline-primary">
                                <i class="fas fa-table"></i> Dati
                            </a>
                            <a href="{{ route('dynamic-pages.edit', $page) }}" class="btn btn-outline-info">
                                <i class="fas fa-edit"></i> Modifica
                            </a>
                            <a href="{{ route('dynamic-pages.show', $page) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-eye"></i> Visualizza
                            </a>
                        </div>
                        <form action="{{ route('dynamic-pages.destroy', $page) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Sei sicuro di voler eliminare questa pagina?')">
                                <i class="fas fa-trash"></i> Elimina
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop

@section('css')
    <style>
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@stop

@section('js')
    <script>
        // Se necessario, aggiungi JavaScript personalizzato qui
    </script>
@stop