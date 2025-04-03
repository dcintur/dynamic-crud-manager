<?php

namespace App\Imports;

use App\Models\DynamicData;
use App\Models\DynamicPage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

class DynamicDataImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected $page;

    public function __construct(DynamicPage $page)
    {
        $this->page = $page;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $fields = $this->page->fields;
        $fieldMap = [];
        
        // Create a mapping between column headers and field names
        foreach ($fields as $field) {
            // Convert to lowercase and replace spaces with underscores for more reliable matching
            $key = strtolower(str_replace(' ', '_', $field->label));
            $fieldMap[$key] = $field->name;
        }
        
        foreach ($rows as $row) {
            $data = [];
            
            foreach ($row as $header => $value) {
                $header = strtolower($header);
                if (isset($fieldMap[$header]) && $value) {
                    $data[$fieldMap[$header]] = $value;
                }
            }
            
            if (!empty($data)) {
                DynamicData::create([
                    'dynamic_page_id' => $this->page->id,
                    'data' => $data
                ]);
            }
        }
    }
}