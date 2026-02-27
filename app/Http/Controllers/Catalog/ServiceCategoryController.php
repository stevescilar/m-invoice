<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::where('company_id', Auth::user()->company_id)
            ->withCount('catalogItems')
            ->latest()
            ->get();

        return view('catalog.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('catalog.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ServiceCategory::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function show(ServiceCategory $category)
    {
        $this->authorizeCategory($category);
        $items = $category->catalogItems()->latest()->get();
        return view('catalog.categories.show', compact('category', 'items'));
    }

    public function edit(ServiceCategory $category)
    {
        $this->authorizeCategory($category);
        return view('catalog.categories.edit', compact('category'));
    }

    public function update(Request $request, ServiceCategory $category)
    {
        $this->authorizeCategory($category);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($request->only(['name', 'description']));

        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(ServiceCategory $category)
    {
        $this->authorizeCategory($category);
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }

    private function authorizeCategory(ServiceCategory $category)
    {
        if ($category->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}
