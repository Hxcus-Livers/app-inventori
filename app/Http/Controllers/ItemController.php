<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\InventoryLog;
use App\Http\Requests\StoreItemRequest;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     */
    public function index()
    {
        $items = Item::with(['category'])
                    ->orderBy('name')
                    ->get();

        return view('items.index', compact('items'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('items.create', compact('categories'));
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(StoreItemRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // Create the item
                $item = Item::create($request->validated());

                // Create inventory log for initial stock
                if ($request->stock > 0) {
                    InventoryLog::create([
                        'item_id' => $item->id,
                        'action_type' => 'add',
                        'quantity' => $request->stock,
                        'notes' => 'Initial stock',
                    ]);
                }
            });

            return redirect()
                ->route('items.index')
                ->with('success', 'Item created successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create item. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified item in storage.
     */
    public function update(StoreItemRequest $request, Item $item)
    {
        try {
            DB::transaction(function () use ($request, $item) {
                $oldStock = $item->stock;
                $newStock = $request->stock;
                
                // Update the item
                $item->update($request->validated());

                // If stock has changed, create a log entry
                if ($oldStock != $newStock) {
                    $difference = $newStock - $oldStock;
                    InventoryLog::create([
                        'item_id' => $item->id,
                        'action_type' => $difference > 0 ? 'add' : 'remove',
                        'quantity' => abs($difference),
                        'notes' => 'Stock updated from ' . $oldStock . ' to ' . $newStock,
                    ]);
                }
            });

            return redirect()
                ->route('items.index')
                ->with('success', 'Item updated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update item. Please try again.');
        }
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        try {
            DB::transaction(function () use ($item) {
                // Create a log entry for the deletion
                if ($item->stock > 0) {
                    InventoryLog::create([
                        'item_id' => $item->id,
                        'action_type' => 'remove',
                        'quantity' => $item->stock,
                        'notes' => 'Item deleted from inventory',
                    ]);
                }
                
                // Delete the item
                $item->delete();
            });

            return redirect()
                ->route('items.index')
                ->with('success', 'Item deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete item. Please try again.');
        }
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item)
    {
        $item->load(['category', 'logs' => function ($query) {
            $query->latest();
        }]);
        
        return view('items.show', compact('item'));
    }
}