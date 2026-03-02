<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\CatalogItem;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogItemController extends Controller
{
    public function index()
    {
        $items = CatalogItem::where('company_id', Auth::user()->company_id)
            ->with('serviceCategory')
            ->latest()
            ->paginate(20);

        $categories = ServiceCategory::where('company_id', Auth::user()->company_id)->get();

        return view('catalog.items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = ServiceCategory::where('company_id', Auth::user()->company_id)->get();

        if ($categories->isEmpty()) {
            return redirect()->route('categories.create')
                ->with('error', 'Please create a service category first before adding items.');
        }

        return view('catalog.items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'default_unit_price' => 'required|numeric|min:0',
            'unit_of_measure' => 'required|string|max:50',
        ]);

        CatalogItem::create([
            'company_id' => Auth::user()->company_id,
            'service_category_id' => $request->service_category_id,
            'name' => $request->name,
            'default_unit_price' => $request->default_unit_price,
            'unit_of_measure' => $request->unit_of_measure,
        ]);

        return redirect()->route('catalog-items.index')->with('success', 'Item added to catalog.');
    }

    public function show(CatalogItem $item)
    {
        $this->authorizeItem($item);
        return redirect()->route('catalog-items.edit', $item);
    }

    public function edit(CatalogItem $item)
    {
        $this->authorizeItem($item);
        $categories = ServiceCategory::where('company_id', Auth::user()->company_id)->get();
        return view('catalog.items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, CatalogItem $item)
    {
        $this->authorizeItem($item);

        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'default_unit_price' => 'required|numeric|min:0',
            'unit_of_measure' => 'required|string|max:50',
        ]);

        $item->update($request->only([
            'service_category_id',
            'name',
            'default_unit_price',
            'unit_of_measure'
        ]));

        return redirect()->route('catalog-items.index')->with('success', 'Item updated.');
    }

    public function destroy(CatalogItem $item)
    {
        $this->authorizeItem($item);
        $item->delete();
        return redirect()->route('catalog-items.index')->with('success', 'Item deleted.');
    }

    private function authorizeItem(CatalogItem $item)
    {
        if ($item->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}
