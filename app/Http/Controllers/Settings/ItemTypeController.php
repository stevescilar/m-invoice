<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ItemType;
use Illuminate\Http\Request;

class ItemTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ]);

        $company = auth()->user()->company;

        ItemType::create([
            'company_id' => $company->id,
            'name'       => $request->name,
            'color'      => $request->color,
            'is_default' => false,
            'is_active'  => true,
            'sort_order' => $company->itemTypes()->count(),
        ]);

        return back()->with('success', 'Item type added.');
    }

    public function update(Request $request, ItemType $itemType)
    {
        abort_if($itemType->company_id !== auth()->user()->company_id, 403);

        $request->validate(['name' => 'required|string|max:50', 'color' => 'required|string|max:7']);

        $itemType->update([
            'name'  => $request->name,
            'color' => $request->color,
        ]);

        return back()->with('success', 'Item type updated.');
    }

    public function toggle(ItemType $itemType)
    {
        abort_if($itemType->company_id !== auth()->user()->company_id, 403);
        abort_if($itemType->is_default, 403); // can't deactivate Material

        $itemType->update(['is_active' => !$itemType->is_active]);

        return back()->with('success', 'Item type ' . ($itemType->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function destroy(ItemType $itemType)
    {
        abort_if($itemType->company_id !== auth()->user()->company_id, 403);
        abort_if($itemType->is_default, 403); // can't delete Material

        $itemType->delete();

        return back()->with('success', 'Item type deleted.');
    }
}