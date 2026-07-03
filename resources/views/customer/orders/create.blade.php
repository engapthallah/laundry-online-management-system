@extends('layouts.customer-portal')

@section('content')
@vite('resources/css/customer-order.css')

@php
    $getIcon = function($name) {
        $name = strtolower($name);
        if (str_contains($name, 'shirt') || str_contains($name, 'suit') || str_contains($name, 'dress') || str_contains($name, 'coat')) {
            return 'fa-shirt';
        }
        if (str_contains($name, 'blanket') || str_contains($name, 'duvet') || str_contains($name, 'bed') || str_contains($name, 'sheet')) {
            return 'fa-mattress-pillow';
        }
        if (str_contains($name, 'dry') || str_contains($name, 'wash')) {
            return 'fa-soap';
        }
        if (str_contains($name, 'shoe') || str_contains($name, 'leather')) {
            return 'fa-shoe-prints';
        }
        if (str_contains($name, 'iron') || str_contains($name, 'press')) {
            return 'fa-person-ironing';
        }
        return 'fa-hands-wash';
    };
@endphp

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Place New Order</h1>
    <a href="{{ route('home') }}" class="btn btn-outline-secondary fw-semibold">
        <i class="fa-solid fa-arrow-left me-2"></i>Back to Home
    </a>
</div>

<!-- Step Wizard Indicators -->
<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
    <div class="card-body p-4">
        <!-- Progress bar info -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fw-bold text-dark fs-5">Order Progress</span>
            <span id="wizardProgressText" class="badge bg-primary px-3 py-2 rounded-pill fw-semibold">Step 1 of 4 (0% Complete)</span>
        </div>
        <div class="progress wizard-progress-bar mb-4">
            <div id="wizardProgressFill" class="progress-bar wizard-progress-fill" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        
        <!-- Step Indicators -->
        <div class="step-indicator-wrapper">
            <div class="step-indicator-item active" id="indicator-1">
                <div class="step-icon-circle">
                    <span class="step-num">1</span>
                    <i class="fa-solid fa-check d-none"></i>
                </div>
                <div class="step-label">Select Services</div>
            </div>
            <div class="step-indicator-item" id="indicator-2">
                <div class="step-icon-circle">
                    <span class="step-num">2</span>
                    <i class="fa-solid fa-check d-none"></i>
                </div>
                <div class="step-label">Schedule Pickup</div>
            </div>
            <div class="step-indicator-item" id="indicator-3">
                <div class="step-icon-circle">
                    <span class="step-num">3</span>
                    <i class="fa-solid fa-check d-none"></i>
                </div>
                <div class="step-label">Payment Method</div>
            </div>
            <div class="step-indicator-item" id="indicator-4">
                <div class="step-icon-circle">
                    <span class="step-num">4</span>
                    <i class="fa-solid fa-check d-none"></i>
                </div>
                <div class="step-label">Confirmation</div>
            </div>
        </div>
    </div>
</div>

<form id="multiStepOrderForm" method="POST" action="{{ route('customer.orders.store') }}">
    @csrf

    <div class="row g-4">
        <!-- Main Form Left Side -->
        <div class="col-12" id="stepsColumn">
            
            <!-- STEP 1: SELECT SERVICES -->
            <div class="step-panel bg-white p-4 rounded-4 shadow-sm" id="step-1-panel">
                <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-hands-wash text-primary me-2"></i>Step 1: Select Services</h4>
                
                @if($errors->has('services'))
                    <div class="alert alert-danger mb-4">{{ $errors->first('services') }}</div>
                @endif

                <div class="row g-3">
                    @foreach($services as $service)
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="card service-card h-100" id="card-{{ $service->id }}"
                                 data-service-id="{{ $service->id }}"
                                 data-service-name="{{ $service->name }}"
                                 data-price-per-item="{{ $service->price_per_item }}"
                                 data-price-per-kg="{{ $service->price_per_kg }}">
                                <div class="card-body p-4 d-flex flex-column">
                                    <!-- Header Section: Icon & Checkbox -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="service-icon-box">
                                            <i class="fa-solid {{ $getIcon($service->name) }}"></i>
                                        </div>
                                        <div class="form-check m-0">
                                            <input class="form-check-input service-checkbox fs-4 cursor-pointer" type="checkbox" 
                                                   name="services[{{ $loop->index }}][selected]" 
                                                   value="1" 
                                                   id="check-{{ $service->id }}"
                                                   data-service-id="{{ $service->id }}"
                                                   data-name="{{ $service->name }}"
                                                   data-price-item="{{ $service->price_per_item }}"
                                                   data-price-kg="{{ $service->price_per_kg }}">
                                            <input type="hidden" name="services[{{ $loop->index }}][service_id]" value="{{ $service->id }}">
                                        </div>
                                    </div>
                                    
                                    <!-- Service Name & Description -->
                                    <h5 class="fw-bold text-dark mb-1">{{ $service->name }}</h5>
                                    <p class="text-secondary small flex-grow-1 mb-3">{{ $service->description }}</p>
                                    
                                    <!-- Pricing Badges -->
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @if($service->price_per_kg > 0)
                                            <span class="badge bg-light text-primary border border-primary-subtle px-2.5 py-1.5 fs-7">
                                                <i class="fa-solid fa-weight-scale me-1"></i> ${{ number_format($service->price_per_kg, 2) }}/kg
                                            </span>
                                        @endif
                                        @if($service->price_per_item > 0)
                                            <span class="badge bg-light text-success border border-success-subtle px-2.5 py-1.5 fs-7">
                                                <i class="fa-solid fa-shirt me-1"></i> ${{ number_format($service->price_per_item, 2) }}/item
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Dynamic Inputs Container (Hidden by default) -->
                                    <div class="row g-2 mt-auto d-none" id="inputs-{{ $service->id }}">
                                        <!-- Quantity -->
                                        <div class="col-6">
                                            <label class="form-label fw-semibold small text-muted mb-1">Quantity</label>
                                            <input type="number" name="services[{{ $loop->index }}][quantity]" value="1" min="1" 
                                                   class="form-control qty-input service-qty" data-service-id="{{ $service->id }}">
                                        </div>
                                        
                                        <!-- Weight (KG) -->
                                        @if($service->price_per_kg > 0)
                                            <div class="col-6">
                                                <label class="form-label fw-semibold small text-muted mb-1">Weight (KG)</label>
                                                <input type="number" step="0.01" min="0.1" name="services[{{ $loop->index }}][weight_kg]" 
                                                       class="form-control weight-input service-weight" placeholder="Optional" data-service-id="{{ $service->id }}">
                                            </div>
                                        @endif

                                        <!-- Special instructions -->
                                        <div class="col-12 mt-2">
                                            <label class="form-label fw-semibold small text-muted mb-1">Care Instructions</label>
                                            <input type="text" name="services[{{ $loop->index }}][care_instructions]" 
                                                   class="form-control notes-input" placeholder="e.g. cold wash, hang dry">
                                        </div>
                                    </div>
                                    
                                    <!-- Subtotal (Hidden by default) -->
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top d-none" id="subtotal-container-{{ $service->id }}">
                                        <span class="text-secondary small fw-medium">Subtotal:</span>
                                        <span class="fs-5 fw-bold text-primary service-subtotal" id="subtotal-val-{{ $service->id }}">$0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- STEP 2: PICKUP AND DELIVERY DETAILS -->
            <div class="step-panel bg-white p-4 rounded-4 shadow-sm d-none" id="step-2-panel">
                <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-calendar-days text-primary me-2"></i>Step 2: Pickup & Delivery</h4>
                
                <div class="row g-4">
                    <!-- Pickup Address -->
                    <div class="col-12">
                        <label for="pickup_address" class="form-label fw-semibold">Pickup Address <span class="text-danger">*</span></label>
                        <textarea name="pickup_address" id="pickup_address" rows="3" class="form-control @error('pickup_address') is-invalid @enderror" required>{{ old('pickup_address', $user->address) }}</textarea>
                        @error('pickup_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Delivery Address -->
                    <div class="col-12">
                        <label for="delivery_address" class="form-label fw-semibold">Delivery Address <span class="text-danger">*</span></label>
                        <textarea name="delivery_address" id="delivery_address" rows="3" class="form-control @error('delivery_address') is-invalid @enderror" required>{{ old('delivery_address', $user->address) }}</textarea>
                        @error('delivery_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pickup Time -->
                    <div class="col-12 col-md-6">
                        <label for="pickup_time" class="form-label fw-semibold">Preferred Pickup Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="pickup_time" id="pickup_time" class="form-control @error('pickup_time') is-invalid @enderror" required>
                        @error('pickup_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Delivery Time -->
                    <div class="col-12 col-md-6">
                        <label for="delivery_time" class="form-label fw-semibold">Preferred Delivery Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="delivery_time" id="delivery_time" class="form-control @error('delivery_time') is-invalid @enderror" required>
                        @error('delivery_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Special Instructions -->
                    <div class="col-12">
                        <label for="special_instructions" class="form-label fw-semibold">Special Instructions for staff (Optional)</label>
                        <textarea name="special_instructions" id="special_instructions" rows="3" class="form-control @error('special_instructions') is-invalid @enderror" placeholder="e.g. Ring doorbell, leave laundry bag in porch..."></textarea>
                        @error('special_instructions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- STEP 3: PAYMENT METHOD -->
            <div class="step-panel bg-white p-4 rounded-4 shadow-sm d-none" id="step-3-panel">
                <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-credit-card text-primary me-2"></i>Step 3: Payment Method</h4>
                
                <div class="row g-3 mb-4">
                    <!-- Cash on Delivery -->
                    <div class="col-12">
                        <div class="card border-2 rounded-4 payment-option-card cursor-pointer transition-all selected" id="pay-card-cash" data-label="Cash on Delivery" data-payment="cash">
                            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fs-2 text-primary"><i class="fa-solid fa-money-bill-wave"></i></div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">Cash on Delivery</h5>
                                        <p class="text-secondary mb-0 small">Pay when your laundry is delivered</p>
                                    </div>
                                </div>
                                <input class="form-check-input payment-radio fs-4 cursor-pointer" type="radio" name="payment_method" id="pay-cash" value="cash" checked>
                            </div>
                        </div>
                    </div>

                    <!-- Zaad Mobile Money -->
                    <div class="col-12">
                        <div class="card border-2 rounded-4 payment-option-card cursor-pointer transition-all" id="pay-card-zaad" data-label="ZAAD" data-payment="zaad">
                            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fs-2 text-primary"><i class="fa-solid fa-mobile-screen-button"></i></div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">Zaad Mobile Money</h5>
                                        <p class="text-secondary mb-0 small">Pay via Zaad mobile transfer</p>
                                    </div>
                                </div>
                                <input class="form-check-input payment-radio fs-4 cursor-pointer" type="radio" name="payment_method" id="pay-zaad" value="zaad">
                            </div>
                        </div>
                    </div>

                    <!-- Edahab Mobile Money -->
                    <div class="col-12">
                        <div class="card border-2 rounded-4 payment-option-card cursor-pointer transition-all" id="pay-card-edahab" data-label="EDAHAB" data-payment="edahab">
                            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fs-2 text-primary"><i class="fa-solid fa-mobile-screen-button"></i></div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">Edahab Mobile Money</h5>
                                        <p class="text-secondary mb-0 small">Pay via Edahab mobile transfer</p>
                                    </div>
                                </div>
                                <input class="form-check-input payment-radio fs-4 cursor-pointer" type="radio" name="payment_method" id="pay-edahab" value="edahab">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs for mobile numbers (needed for form request validation) -->
                <div class="mb-3 d-none" id="phone-input-container">
                    <label for="payment_phone" class="form-label fw-bold">Mobile Wallet Phone Number</label>
                    <input type="text" name="payment_phone" id="payment_phone" class="form-control">
                </div>

                <!-- Sub-step A — Show merchant number -->
                <div id="mobilePayInstructions" style="display:none;" class="alert alert-info border-0 rounded-4 p-4 mt-3">
                    <h6 class="fw-bold text-info-emphasis mb-2"><i class="fa-solid fa-circle-info me-2"></i>Payment Instructions:</h6>
                    <p class="mb-2">Transfer to this Merchant Number:</p>
                    <div class="fs-4 fw-bold text-primary mb-3 bg-white d-inline-block px-3 py-2 rounded-3 border" id="merchantNumberDisplay"></div>
                    <p class="small text-muted mb-0">After completing the transfer, please fill in the proof of payment details below.</p>
                </div>

                <!-- Sub-step B — Customer proof fields -->
                <div id="mobilePayProofFields" style="display:none;" class="card border-0 bg-light rounded-4 p-4 mt-3">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>Proof of Payment</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Your Wallet Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="walletPhoneInput" placeholder="e.g. +25261XXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Sender Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="senderNameInput" placeholder="Name shown on transfer receipt">
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 4: CONFIRMATION -->
            <div class="step-panel bg-white p-4 rounded-4 shadow-sm d-none" id="step-4-panel">
                <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-clipboard-check text-primary me-2"></i>Step 4: Confirm Order</h4>
                <input type="hidden" name="customer_payment_confirmed" id="customerPaymentConfirmed" value="0">
                
                <div class="alert alert-info border-0 rounded-3 mb-4">
                    Please review your order details below. Click <strong>Confirm Order</strong> at the bottom to finalize.
                </div>

                <div class="row g-3">
                    <!-- Selected Services Summary -->
                    <div class="col-12">
                        <div class="card border-0 bg-light rounded-4 mb-3">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-muted small text-uppercase mb-3 d-flex align-items-center">
                                    <i class="fa-solid fa-hands-wash text-primary me-2"></i>Selected Services
                                </h6>
                                <div class="list-group list-group-flush rounded-3 bg-transparent" id="confirm-services-list">
                                    <!-- Dynamic items -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pickup & Delivery Summary -->
                    <div class="col-md-6">
                        <div class="card border-0 bg-light rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-muted small text-uppercase mb-3 d-flex align-items-center">
                                    <i class="fa-solid fa-calendar-days text-primary me-2"></i>Pickup Schedule
                                </h6>
                                <div class="mb-2 text-dark">
                                    <strong>Time:</strong> <span id="confirm-pickup-time"></span>
                                </div>
                                <div class="text-secondary small">
                                    <strong>Address:</strong> <span id="confirm-pickup-addr"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-0 bg-light rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-muted small text-uppercase mb-3 d-flex align-items-center">
                                    <i class="fa-solid fa-truck text-primary me-2"></i>Delivery Schedule
                                </h6>
                                <div class="mb-2 text-dark">
                                    <strong>Time:</strong> <span id="confirm-delivery-time"></span>
                                </div>
                                <div class="text-secondary small">
                                    <strong>Address:</strong> <span id="confirm-delivery-addr"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment & Completion Summary -->
                    <div class="col-md-6">
                        <div class="card border-0 bg-light rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-muted small text-uppercase mb-3 d-flex align-items-center">
                                    <i class="fa-solid fa-credit-card text-primary me-2"></i>Payment Details
                                </h6>
                                <div class="mb-2 text-dark">
                                    <strong>Method:</strong> <span id="confirm-payment-method" class="text-capitalize"></span>
                                </div>
                                <div class="text-secondary small d-none" id="confirm-payment-phone-container">
                                    <strong>Phone:</strong> <span id="confirm-payment-phone"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 bg-light rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-muted small text-uppercase mb-3 d-flex align-items-center">
                                    <i class="fa-solid fa-clock text-primary me-2"></i>Estimated Completion
                                </h6>
                                <div class="text-dark fs-5 fw-semibold" id="confirm-est-completion"></div>
                                <div class="text-muted small mt-1">Ready for pickup/delivery at this time.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Special Instructions Summary -->
                    <div class="col-12">
                        <div class="card border-0 bg-light rounded-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center">
                                    <i class="fa-solid fa-comment-dots text-primary me-2"></i>Special Instructions
                                </h6>
                                <p class="mb-0 text-dark small" id="confirm-special-instr"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Controls -->
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary fw-bold px-4 py-2 rounded-3 d-none" id="btn-back">
                    &larr; Previous
                </button>
                <div class="ms-auto"></div>

                {{-- Shown for Cash on Delivery --}}
                <button type="button"
                        class="btn btn-primary px-4"
                        id="cashNextBtn"
                        onclick="goToStep(4)"
                        style="display:none;">
                    Next Step &rarr;
                </button>

                {{-- Shown for Zaad / Edahab after filling proof fields --}}
                <button type="button"
                        class="btn btn-success px-4 fw-semibold"
                        id="iHavePaidBtn"
                        onclick="handleIHavePaid()"
                        style="display:none;">
                    ✅ I Have Paid — Proceed to Confirmation
                </button>

                <button type="button" class="btn btn-primary fw-bold px-4 py-2 rounded-3" id="btn-next">
                    Next Step &rarr;
                </button>
                
                <button type="submit" class="btn btn-success fw-bold px-4 py-2 rounded-3 d-none" id="btn-confirm">
                    ✓ Confirm Order
                </button>
            </div>
        </div>

        <!-- Sticky Right Summary Panel -->
        <div class="col-12 col-md-4" id="orderSidebarColumn" style="display:none;">
            <div class="card sticky-summary-card bg-white border-0 shadow">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="fa-solid fa-receipt text-primary me-2"></i>Order Summary
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Selected Services list -->
                    <div class="d-flex flex-column gap-2 mb-3" id="summaryServicesList">
                        <div class="text-center py-4 text-muted small" id="summary-empty-msg">
                            No services selected.
                        </div>
                    </div>
                    
                    <hr class="text-secondary opacity-25 my-3">
                    
                    <!-- Details Grid -->
                    <div class="d-flex flex-column gap-2 mb-3 small text-muted">
                        <!-- Pickup -->
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-truck-pickup mt-1 text-primary"></i>
                            <div>
                                <span class="fw-bold d-block text-dark">Pickup Address:</span>
                                <span id="summaryPickupAddress">Not provided</span>
                            </div>
                        </div>
                        
                        <!-- Delivery -->
                        <div class="d-flex align-items-start gap-2 mt-2">
                            <i class="fa-solid fa-truck mt-1 text-primary"></i>
                            <div>
                                <span class="fw-bold d-block text-dark">Delivery Address:</span>
                                <span id="summaryDeliveryAddress">Not provided</span>
                            </div>
                        </div>
                        
                        <!-- Est Completion -->
                        <div class="d-flex align-items-start gap-2 mt-2">
                            <i class="fa-solid fa-calendar-check mt-1 text-primary"></i>
                            <div>
                                <span class="fw-bold d-block text-dark">Estimated Completion:</span>
                                <span id="summaryEstCompletion">Not scheduled</span>
                            </div>
                        </div>
                    </div>

                    <!-- Grand Total -->
                    <div id="summaryPaymentRow" style="display:none;">
                        <hr class="text-secondary opacity-25 my-3">
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <span class="text-muted small d-block mb-1">Payment Channel</span>
                            <div class="fw-bold text-capitalize text-dark" id="summaryPaymentLabel">Cash on Delivery</div>
                        </div>
                    </div>
                    
                    <hr class="text-secondary opacity-25 my-3">
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">Grand Total:</span>
                        <span class="fw-bold text-primary fs-4" id="summaryGrandTotal">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
@vite('resources/js/customer-order.js')
@endsection
