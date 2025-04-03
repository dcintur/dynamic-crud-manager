<?php

namespace App\Http\Controllers;

use App\Models\DynamicPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DynamicPageController extends Controller
{
    public function index()
    {
        $pages = DynamicPage::orderBy('order')->get();
        return view('dynamic-pages.card-index', compact('pages'));
    }

    public function create()
    {
        return view('dynamic-pages.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'menu_group' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        // Generate slug from name
        $validatedData['slug'] = Str::slug($validatedData['name']);
        
        $page = DynamicPage::create($validatedData);

        return redirect()->route('dynamic-pages.edit', $page)
            ->with('success', 'Page created successfully. Now add fields to it.');
    }

    public function show(DynamicPage $dynamicPage)
    {
        return view('dynamic-pages.show', ['page' => $dynamicPage]);
    }

    public function edit(DynamicPage $dynamicPage)
    {
        return view('dynamic-pages.edit', ['page' => $dynamicPage]);
    }

    public function update(Request $request, DynamicPage $dynamicPage)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'menu_group' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        // Update slug only if name has changed
        if ($dynamicPage->name !== $validatedData['name']) {
            $validatedData['slug'] = Str::slug($validatedData['name']);
        }

        $dynamicPage->update($validatedData);

        return redirect()->route('dynamic-pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(DynamicPage $dynamicPage)
    {
        $dynamicPage->delete();
        return redirect()->route('dynamic-pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}