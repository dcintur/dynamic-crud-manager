<?php

namespace App\Http\Controllers;

use App\Models\DynamicData;
use App\Models\DynamicPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DynamicDataExport;
use App\Imports\DynamicDataImport;
use Illuminate\Support\Str;

class DynamicDataController extends Controller
{
    public function index(Request $request, DynamicPage $page)
    {
        $query = DynamicData::where('dynamic_page_id', $page->id);
        
        // Apply advanced filters if set
        if ($request->has('filters')) {
            $filters = $request->filters;
            
            foreach ($filters as $index => $filter) {
                if (empty($filter['field']) || empty($filter['value'])) {
                    continue;
                }
                
                $field = $filter['field'];
                $operator = $filter['operator'];
                $value = $filter['value'];
                $join = isset($filter['join']) ? $filter['join'] : 'and';
                
                // Skip the first filter join
                $method = ($index === 0 || $join === 'and') ? 'where' : 'orWhere';
                
                switch ($operator) {
                    case 'equals':
                        $query->$method("data->$field", $value);
                        break;
                        
                    case 'contains':
                        $query->$method("data->$field", 'like', "%$value%");
                        break;
                        
                    case 'starts_with':
                        $query->$method("data->$field", 'like', "$value%");
                        break;
                        
                    case 'ends_with':
                        $query->$method("data->$field", 'like', "%$value");
                        break;
                        
                    case 'greater_than':
                        $query->$method("data->$field", '>', $value);
                        break;
                        
                    case 'less_than':
                        $query->$method("data->$field", '<', $value);
                        break;
                }
            }
        }
        
        // Apply simple filter if set (for backward compatibility)
        elseif ($request->filled('filter_field') && $request->filled('filter_value')) {
            // ... existing simple filter code ...
        }
        
        // Apply sorting if set
        if ($request->filled('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy("data->{$request->sort}", $direction);
        }
        
        $data = $query->paginate(10)->withQueryString();
        
        return view('dynamic-data.index', compact('page', 'data'));
    }

    public function create(DynamicPage $page)
    {
        return view('dynamic-data.create', compact('page'));
    }

    public function store(Request $request)
    {
        $page = DynamicPage::findOrFail($request->dynamic_page_id);
        
        // Build validation rules based on fields
        $rules = [];
        foreach ($page->fields as $field) {
            $fieldRules = [];
            
            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            
            if ($field->is_unique) {
                $fieldRules[] = 'unique:dynamic_data,data->' . $field->name;
            }
            
            // Add type-specific validation
            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                case 'file':
                    $fieldRules = ['file', 'max:10240']; // 10MB max
                    break;
            }
            
            $rules['data.' . $field->name] = implode('|', $fieldRules);
        }
        
        $validatedData = $request->validate($rules);
        $data = $validatedData['data'];
        
        // Handle file uploads
        foreach ($page->fields as $field) {
            if ($field->type === 'file' && $request->hasFile('data.' . $field->name)) {
                $file = $request->file('data.' . $field->name);
                $path = $file->store('dynamic-data', 'public');
                $data[$field->name] = $path;
            }
        }
        
        DynamicData::create([
            'dynamic_page_id' => $page->id,
            'data' => $data
        ]);
        
        return redirect()->route('dynamic-data.page', $page)
            ->with('success', 'Record added successfully.');
    }

    public function edit(DynamicData $dynamicData)
    {
        // Debug: stampa l'ID del record e l'ID della pagina
        \Log::info('DynamicData ID: ' . $dynamicData->id);
        \Log::info('Page ID: ' . $dynamicData->dynamic_page_id);
        
        $page = $dynamicData->page;
        
        // Debug: verifica se $page è null
        \Log::info('Page is null: ' . ($page === null ? 'yes' : 'no'));
        
        if ($page) {
            \Log::info('Page name: ' . $page->name);
        }
        
        return view('dynamic-data.edit', compact('dynamicData', 'page'));
    }

    public function update(Request $request, DynamicData $dynamicData)
    {
        $page = $dynamicData->page;
        
        // Build validation rules based on fields
        $rules = [];
        foreach ($page->fields as $field) {
            $fieldRules = [];
            
            if ($field->is_required && $field->type !== 'file') {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            
            if ($field->is_unique) {
                $fieldRules[] = 'unique:dynamic_data,data->' . $field->name . ',' . $dynamicData->id;
            }
            
            // Add type-specific validation
            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                case 'file':
                    if ($request->hasFile('data.' . $field->name)) {
                        $fieldRules = ['file', 'max:10240']; // 10MB max
                    }
                    break;
            }
            
            if (!empty($fieldRules)) {
                $rules['data.' . $field->name] = implode('|', $fieldRules);
            }
        }
        
        $validatedData = $request->validate($rules);
        $data = $dynamicData->data;
        
        // Update all non-file fields
        foreach ($validatedData['data'] as $key => $value) {
            $field = $page->fields->where('name', $key)->first();
            if ($field && $field->type !== 'file') {
                $data[$key] = $value;
            }
        }
        
        // Handle file uploads
        foreach ($page->fields as $field) {
            if ($field->type === 'file' && $request->hasFile('data.' . $field->name)) {
                // Delete previous file if exists
                if (isset($data[$field->name])) {
                    Storage::disk('public')->delete($data[$field->name]);
                }
                
                $file = $request->file('data.' . $field->name);
                $path = $file->store('dynamic-data', 'public');
                $data[$field->name] = $path;
            }
        }
        
        $dynamicData->update([
            'data' => $data
        ]);
        
        return redirect()->route('dynamic-data.page', $page)
            ->with('success', 'Record updated successfully.');
    }

    public function destroy(DynamicData $dynamicData)
    {
        $page = $dynamicData->page;
        
        // Delete files associated with this record
        foreach ($page->fields as $field) {
            if ($field->type === 'file' && isset($dynamicData->data[$field->name])) {
                Storage::disk('public')->delete($dynamicData->data[$field->name]);
            }
        }
        
        $dynamicData->delete();
        
        return redirect()->route('dynamic-data.page', $page)
            ->with('success', 'Record deleted successfully.');
    }

    /**
     * Export data to CSV, Excel, or PDF
     */
    public function export(Request $request, DynamicPage $page)
    {
        $format = $request->format ?? 'csv';
        $filename = Str::slug($page->name) . '_export_' . date('Y-m-d');
        
        // Apply filters if they exist
        $query = DynamicData::where('dynamic_page_id', $page->id);
        
        if ($request->filled('filter_field') && $request->filled('filter_value')) {
            $field = $request->filter_field;
            $operator = $request->filter_operator;
            $value = $request->filter_value;
            
            switch ($operator) {
                case 'equals':
                    $query->where("data->$field", $value);
                    break;
                    
                case 'contains':
                    $query->where("data->$field", 'like', "%$value%");
                    break;
                    
                case 'starts_with':
                    $query->where("data->$field", 'like', "$value%");
                    break;
                    
                case 'ends_with':
                    $query->where("data->$field", 'like', "%$value");
                    break;
                    
                case 'greater_than':
                    $query->where("data->$field", '>', $value);
                    break;
                    
                case 'less_than':
                    $query->where("data->$field", '<', $value);
                    break;
            }
        }
        
        // Apply sorting if set
        if ($request->filled('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy("data->{$request->sort}", $direction);
        }
        
        $data = $query->get();
        
        // For PDF use different handling
        if ($format === 'pdf') {
            // Make sure to install barryvdh/laravel-dompdf first
            $fields = $page->fields->where('is_visible', true);
            
            $pdf = app('dompdf.wrapper');
            $view = view('exports.pdf', compact('page', 'data', 'fields'))->render();
            $pdf->loadHTML($view);
            
            return $pdf->download($filename . '.pdf');
        }
        
        // For CSV and Excel use Excel Facade
        return Excel::download(new DynamicDataExport($page, $data), $filename . '.' . $format);
    }

    /**
     * Import data from CSV
     */
    public function import(Request $request, DynamicPage $page)
    {
        $request->validate([
            'importFile' => 'required|file|mimes:csv,txt,xls,xlsx',
        ]);
        
        Excel::import(new DynamicDataImport($page), $request->file('importFile'));
        
        return redirect()->route('dynamic-data.page', $page)
            ->with('success', 'Data imported successfully.');
    }
}