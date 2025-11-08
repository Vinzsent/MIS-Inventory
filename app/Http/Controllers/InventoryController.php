<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Inventory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Inventory::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->get('category') !== '') {
            $query->where('category', $request->get('category'));
        }

        $inventories = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Inventory/Index', [
            'inventories' => $inventories,
            'filters' => $request->only(['search', 'category']),
            'categories' => Inventory::distinct()->whereNotNull('category')->pluck('category')->sort()->values(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Inventory/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        Inventory::create($data);

        return redirect()->route('inventory.index')->with('success', 'Inventory item created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory): Response
    {
        return Inertia::render('Inventory/Edit', [
            'inventory' => $inventory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventoryRequest $request, Inventory $inventory): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', false);

        $inventory->update($data);

        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory): RedirectResponse
    {
        $inventory->delete();

        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully.');
    }
}
