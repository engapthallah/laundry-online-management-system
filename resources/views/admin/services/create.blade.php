@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Add New Service</h1>
    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary fw-semibold">
        <i class="fa-solid fa-arrow-left me-2"></i>Back to Services Directory
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-md-5">
        <form method="POST" action="{{ route('admin.services.store') }}">
            @csrf

            <div class="row g-4">
                <!-- Name -->
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label fw-semibold">Service Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Dry Cleaning">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" checked>
                        <label class="form-check-label fw-semibold ms-2" for="is_active">Activate service immediately</label>
                    </div>
                </div>

                <!-- Price Per KG -->
                <div class="col-12 col-md-6">
                    <label for="price_per_kg" class="form-label fw-semibold">Price per KG ($)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="price_per_kg" id="price_per_kg" class="form-control @error('price_per_kg') is-invalid @enderror" value="{{ old('price_per_kg', '0.00') }}" required>
                        @error('price_per_kg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text text-muted small">Set to 0.00 if pricing is per item only.</div>
                </div>

                <!-- Price Per Item -->
                <div class="col-12 col-md-6">
                    <label for="price_per_item" class="form-label fw-semibold">Price per Item ($)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="price_per_item" id="price_per_item" class="form-control @error('price_per_item') is-invalid @enderror" value="{{ old('price_per_item', '0.00') }}" required>
                        @error('price_per_item')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text text-muted small">Set to 0.00 if pricing is per KG only.</div>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">Service Description</label>
                    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Describe the details of this laundry service... (types of clothes accepted, washing techniques, ironing options etc.)">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-flex justify-content-end gap-2 mt-5">
                <a href="{{ route('admin.services.index') }}" class="btn btn-light fw-bold px-4">Cancel</a>
                <button type="submit" class="btn btn-primary fw-bold px-4">Save Service</button>
            </div>
        </form>
    </div>
</div>
@endsection
