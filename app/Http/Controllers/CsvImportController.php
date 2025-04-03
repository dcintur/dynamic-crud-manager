<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\DynamicPage;
use App\Models\DynamicField;

class CsvImportController extends Controller
{
    public function showImportForm()
    {
        return view('csv-import.form');
    }
    
    public function processImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'page_name' => 'required|string|max:255',
            'menu_group' => 'nullable|string|max:255',
            'has_header' => 'boolean'
        ]);
        
        $file = $request->file('csv_file');
        $hasHeader = $request->has('has_header');
        $pageName = $request->page_name;
        $menuGroup = $request->menu_group;
        
        // Parse CSV file
        $csvData = array_map('str_getcsv', file($file->getPathname()));
        
        // Extract headers (either from first row or generate them)
        if ($hasHeader && count($csvData) > 0) {
            $headers = array_shift($csvData);
        } else {
            // Generate headers (Column1, Column2, etc.)
            $headers = [];
            if (count($csvData) > 0) {
                for ($i = 0; $i < count($csvData[0]); $i++) {
                    $headers[] = 'Column' . ($i + 1);
                }
            }
        }
        
        // Create the dynamic page
        $page = DynamicPage::create([
            'name' => $pageName,
            'slug' => Str::slug($pageName),
            'menu_group' => $menuGroup,
            'icon' => 'bi bi-table',
            'is_active' => true,
        ]);
        
        // Create fields based on the headers
        foreach ($headers as $index => $header) {
            // Determine field type based on the data
            $fieldType = $this->determineFieldType($csvData, $index);
            
            // Create field
            DynamicField::create([
                'dynamic_page_id' => $page->id,
                'name' => Str::snake($header),
                'label' => $header,
                'type' => $fieldType,
                'is_required' => false,
                'is_searchable' => true,
                'is_sortable' => true,
                'is_visible' => true,
                'order' => $index,
            ]);
        }
        
        // Import data if there are any rows
        if (count($csvData) > 0) {
            $dataRecords = [];
            
            foreach ($csvData as $row) {
                $data = [];
                
                foreach ($headers as $index => $header) {
                    if (isset($row[$index])) {
                        $data[Str::snake($header)] = $row[$index];
                    }
                }
                
                if (!empty($data)) {
                    $dataRecords[] = [
                        'dynamic_page_id' => $page->id,
                        'data' => json_encode($data),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            // Bulk insert records
            if (!empty($dataRecords)) {
                \DB::table('dynamic_data')->insert($dataRecords);
            }
        }
        
        return redirect()->route('dynamic-pages.edit', $page)
            ->with('success', 'CSV imported successfully! Page created with ' . count($headers) . ' fields and ' . count($csvData) . ' records.');
    }
    
    private function determineFieldType($data, $columnIndex)
    {
        // Default type
        $type = 'text';
        
        // Sample some values
        $sampleSize = min(count($data), 10);
        $samples = [];
        
        for ($i = 0; $i < $sampleSize; $i++) {
            if (isset($data[$i][$columnIndex])) {
                $samples[] = $data[$i][$columnIndex];
            }
        }
        
        // Skip if no samples
        if (empty($samples)) {
            return $type;
        }
        
        // Check if all samples are numeric
        $allNumeric = true;
        foreach ($samples as $sample) {
            if (!is_numeric($sample) && !empty($sample)) {
                $allNumeric = false;
                break;
            }
        }
        
        if ($allNumeric) {
            $type = 'number';
        }
        
        // Check if all samples look like dates
        $allDates = true;
        foreach ($samples as $sample) {
            if (!empty($sample)) {
                try {
                    new \DateTime($sample);
                } catch (\Exception $e) {
                    $allDates = false;
                    break;
                }
            }
        }
        
        if ($allDates) {
            $type = 'date';
        }
        
        // Check if all samples look like emails
        $allEmails = true;
        foreach ($samples as $sample) {
            if (!empty($sample) && !filter_var($sample, FILTER_VALIDATE_EMAIL)) {
                $allEmails = false;
                break;
            }
        }
        
        if ($allEmails) {
            $type = 'email';
        }
        
        return $type;
    }
}