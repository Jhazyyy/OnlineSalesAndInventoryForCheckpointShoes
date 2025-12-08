<?php

namespace App\Http\Controllers;

use App\Models\MarkupPrice;
use Illuminate\Http\Request;

class MarkupPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $markupPrices = MarkupPrice::orderBy('updated_at', 'desc')->paginate(10);
        return view('master_data.markup_prices.index', compact('markupPrices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master_data.markup_prices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:markup_prices,name',
            'description' => 'nullable|string',
            'markup_percentage' => 'required|numeric|min:0|max:1000',
            'is_active' => 'boolean',
        ]);

        MarkupPrice::create($validated);

        return redirect()->route('master_data.markup_prices.index')
            ->with('success', 'Markup price created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarkupPrice $markupPrice)
    {
        return view('master_data.markup_prices.show', compact('markupPrice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarkupPrice $markupPrice)
    {
        return view('master_data.markup_prices.edit', compact('markupPrice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MarkupPrice $markupPrice)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:markup_prices,name,' . $markupPrice->id,
            'description' => 'nullable|string',
            'markup_percentage' => 'required|numeric|min:0|max:1000',
            'is_active' => 'boolean',
        ]);

        $markupPrice->update($validated);

        return redirect()->route('master_data.markup_prices.index')
            ->with('success', 'Markup price updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarkupPrice $markupPrice)
    {
        $markupPrice->delete();

        return redirect()->route('master_data.markup_prices.index')
            ->with('success', 'Markup price deleted successfully.');
    }
}
