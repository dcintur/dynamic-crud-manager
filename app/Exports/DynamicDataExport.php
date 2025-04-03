<?php

namespace App\Exports;

use App\Models\DynamicData;
use App\Models\DynamicPage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DynamicDataExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $page;
    protected $filteredData;

    public function __construct(DynamicPage $page, $filteredData = null)
    {
        $this->page = $page;
        $this->filteredData = $filteredData;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->filteredData) {
            return $this->filteredData;
        }
        return DynamicData::where('dynamic_page_id', $this->page->id)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $headings = [];
        
        foreach ($this->page->fields as $field) {
            if ($field->is_visible) {
                $headings[] = $field->label;
            }
        }
        
        return $headings;
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        $data = [];
        
        foreach ($this->page->fields as $field) {
            if ($field->is_visible) {
                if (isset($row->data[$field->name])) {
                    if ($field->type === 'file') {
                        $data[] = asset('storage/' . $row->data[$field->name]);
                    } else {
                        $data[] = $row->data[$field->name];
                    }
                } else {
                    $data[] = '';
                }
            }
        }
        
        return $data;
    }
}