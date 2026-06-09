<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Service::query();

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->input('search')}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $services = $query->latest()->get();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:services,name'],
            'description' => ['nullable', 'string'],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'price_per_item' => ['required', 'numeric', 'min:0'],
        ]);

        $service = new Service();
        $service->name = $validated['name'];
        $service->slug = Str::slug($validated['name']);
        $service->description = $validated['description'];
        $service->price_per_kg = $validated['price_per_kg'];
        $service->price_per_item = $validated['price_per_item'];
        $service->is_active = $request->has('is_active');
        $service->save();

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('services', 'name')->ignore($service->id)],
            'description' => ['nullable', 'string'],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'price_per_item' => ['required', 'numeric', 'min:0'],
        ]);

        $service->name = $validated['name'];
        $service->slug = Str::slug($validated['name']);
        $service->description = $validated['description'];
        $service->price_per_kg = $validated['price_per_kg'];
        $service->price_per_item = $validated['price_per_item'];
        $service->is_active = $request->has('is_active');
        $service->save();

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        // Check if service has order items
        if ($service->orderItems()->exists()) {
            return redirect()->route('admin.services.index')->with('error', 'This service cannot be deleted because it has associated orders.');
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }

    /**
     * Toggle the service's active status.
     */
    public function toggleStatus(Service $service): JsonResponse|RedirectResponse
    {
        $service->is_active = !$service->is_active;
        $service->save();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $service->is_active,
                'message' => 'Service status toggled successfully.'
            ]);
        }

        return back()->with('success', 'Service status toggled successfully.');
    }
}
