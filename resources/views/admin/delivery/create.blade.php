@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Assign Delivery Agent</h1>
    <a href="{{ route('admin.delivery.index') }}" class="btn btn-outline-secondary fw-semibold">
        <i class="fa-solid fa-arrow-left me-2"></i>Back to Assignments
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-md-5">
        <form method="POST" action="{{ route('admin.delivery.store') }}">
            @csrf

            <div class="row g-4">
                <!-- Order Select -->
                <div class="col-12 col-md-6">
                    <label for="order_id" class="form-label fw-semibold">Select Order (Ready for Delivery)</label>
                    <select name="order_id" id="order_id" class="form-select @error('order_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Choose an order...</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                Order #{{ $order->order_number }} - {{ $order->customer->name ?? 'Customer' }} (${{ number_format($order->total_price, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('order_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small">Only orders with the status "Ready for Delivery" that have not been assigned are displayed.</div>
                </div>

                <!-- Agent Select -->
                <div class="col-12 col-md-6">
                    <label for="delivery_agent_id" class="form-label fw-semibold">Select Delivery Agent</label>
                    <select name="delivery_agent_id" id="delivery_agent_id" class="form-select @error('delivery_agent_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Choose an agent...</option>
                        @foreach($deliveryAgents as $agent)
                            <option value="{{ $agent->id }}" {{ old('delivery_agent_id') == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }} ({{ $agent->phone ?? 'No phone' }})
                            </option>
                        @endforeach
                    </select>
                    @error('delivery_agent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small">Only active delivery courier accounts are displayed.</div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-5">
                <a href="{{ route('admin.delivery.index') }}" class="btn btn-light fw-bold px-4">Cancel</a>
                <button type="submit" class="btn btn-primary fw-bold px-4">Assign Delivery</button>
            </div>
        </form>
    </div>
</div>
@endsection
