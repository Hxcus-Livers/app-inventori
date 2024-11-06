<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Checkout;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $items = Item::where('stock', '>', 0)->get();
        return view('checkouts.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'purpose' => 'required|string'
        ]);

        $item = Item::findOrFail($validated['item_id']);

        if ($item->stock < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock');
        }

        DB::transaction(function () use ($validated, $item) {
            // Create checkout record
            Checkout::create($validated);

            // Update item stock
            $item->update([
                'stock' => $item->stock - $validated['quantity']
            ]);

            // Create log entry
            InventoryLog::create([
                'item_id' => $item->id,
                'action_type' => 'checkout',
                'quantity' => $validated['quantity'],
                'notes' => $validated['purpose']
            ]);
        });

        return redirect()->route('history.index')->with('success', 'Item checked out successfully');
    }
}
