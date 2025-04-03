<?php

namespace App\Http\Controllers;

use App\Models\DynamicField;
use App\Models\DynamicPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DynamicFieldController extends Controller
{
    public function create(Request $request)
    {
        $page = DynamicPage::findOrFail($request->page_id);
        $fieldTypes = $this->getFieldTypes();
        
        return view('dynamic-fields.create', compact('page', 'fieldTypes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'dynamic_page_id' => 'required|exists:dynamic_pages,id',
            'label' => 'required|string|max:255',
            'type' => 'required|string',
            'is_required' => 'boolean',
            'is_unique' => 'boolean',
            'is_searchable' => 'boolean',
            'is_sortable' => 'boolean',
            'is_visible' => 'boolean',
            'options' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        // Generate a snake_case name from the label
        $validatedData['name'] = Str::snake($validatedData['label']);
        
        // Convert options string to JSON if provided
        if (!empty($validatedData['options'])) {
            $options = explode("\n", $validatedData['options']);
            $formattedOptions = [];
            
            foreach ($options as $option) {
                $option = trim($option);
                if (!empty($option)) {
                    $formattedOptions[] = $option;
                }
            }
            
            $validatedData['options'] = $formattedOptions;
        } else {
            $validatedData['options'] = null;
        }

        DynamicField::create($validatedData);

        return redirect()->route('dynamic-pages.edit', $validatedData['dynamic_page_id'])
            ->with('success', 'Field added successfully.');
    }

    public function edit(DynamicField $dynamicField)
    {
        $fieldTypes = $this->getFieldTypes();
        $optionsString = '';
        
        if (!empty($dynamicField->options) && is_array($dynamicField->options)) {
            $optionsString = implode("\n", $dynamicField->options);
        }
        
        return view('dynamic-fields.edit', compact('dynamicField', 'fieldTypes', 'optionsString'));
    }

    public function update(Request $request, DynamicField $dynamicField)
    {
        $validatedData = $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|string',
            'is_required' => 'boolean',
            'is_unique' => 'boolean',
            'is_searchable' => 'boolean',
            'is_sortable' => 'boolean',
            'is_visible' => 'boolean',
            'options' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        // Update name if label changed
        if ($dynamicField->label !== $validatedData['label']) {
            $validatedData['name'] = Str::snake($validatedData['label']);
        }
        
        // Convert options string to JSON if provided
        if (!empty($validatedData['options'])) {
            $options = explode("\n", $validatedData['options']);
            $formattedOptions = [];
            
            foreach ($options as $option) {
                $option = trim($option);
                if (!empty($option)) {
                    $formattedOptions[] = $option;
                }
            }
            
            $validatedData['options'] = $formattedOptions;
        } else {
            $validatedData['options'] = null;
        }

        $dynamicField->update($validatedData);

        return redirect()->route('dynamic-pages.edit', $dynamicField->dynamic_page_id)
            ->with('success', 'Field updated successfully.');
    }

    public function destroy(DynamicField $dynamicField)
    {
        $pageId = $dynamicField->dynamic_page_id;
        $dynamicField->delete();
        
        return redirect()->route('dynamic-pages.edit', $pageId)
            ->with('success', 'Field deleted successfully.');
    }

    private function getFieldTypes()
    {
        return [
            'text' => 'Text',
            'textarea' => 'Text Area',
            'number' => 'Number',
            'email' => 'Email',
            'password' => 'Password',
            'date' => 'Date',
            'datetime' => 'Date & Time',
            'select' => 'Select Dropdown',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Buttons',
            'file' => 'File Upload',
            'color' => 'Color Picker',
            'url' => 'URL',
            'tel' => 'Telephone Number',
            'hidden' => 'Hidden Field'
        ];
    }
}