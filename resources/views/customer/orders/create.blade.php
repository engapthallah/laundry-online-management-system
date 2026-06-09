@extends('layouts.customer')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Place New Order</h1>
    <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary fw-semibold">
        <i class="fa-solid fa-arrow-left me-2"></i>Back to Home
    </a>
</div>

<!-- Step Wizard Indicators -->
<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
    <div class="card-body p-4">
        <div class="row text-center g-2">
            <div class="col-3 step-indicator text-primary fw-bold" id="indicator-1">
                <span class="badge bg-primary rounded-circle px-2.5 py-2 fs-6 mb-1">1</span>
                <div class="small d-none d-sm-block">Select Services</div>
            </div>
            <div class="col-3 step-indicator text-muted" id="indicator-2">
                <span class="badge bg-secondary rounded-circle px-2.5 py-2 fs-6 mb-1">2</span>
                <div class="small d-none d-sm-block">Schedule Pickup</div>
            </div>
            <div class="col-3 step-indicator text-muted" id="indicator-3">
                <span class="badge bg-secondary rounded-circle px-2.5 py-2 fs-6 mb-1">3</span>
                <div class="small d-none d-sm-block">Payment Method</div>
            </div>
            <div class="col-3 step-indicator text-muted" id="indicator-4">
                <span class="badge bg-secondary rounded-circle px-2.5 py-2 fs-6 mb-1">4</span>
                <div class="small d-none d-sm-block">Confirmation</div>
            </div>
        </div>
    </div>
</div>

<form id="multiStepOrderForm" method="POST" action="{{ route('customer.orders.store') }}">
    @csrf

    <div class="row g-4">
        <!-- Main Form Left Side -->
        <div class="col-12 col-lg-8">
            
            <!-- STEP 1: SELECT SERVICES -->
            <div class="step-panel bg-white p-4 rounded-4 shadow-sm" id="step-1-panel">
                <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-hands-wash text-primary me-2"></i>Step 1: Select Services</h4>
                
                @if($errors->has('services'))
                    <div class="alert alert-danger mb-4">{{ $errors->first('services') }}</div>
                @endif

                <div class="row g-4">
                    @foreach($services as $service)
                        <div class="col-12">
                            <div class="card border border-2 rounded-4 service-card transition-all" id="card-{{ $service->id }}">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start gap-3">
                                        <!-- Selection Checkbox -->
                                        <div class="form-check m-0 pt-1">
                                            <input class="form-check-input service-checkbox fs-4" type="checkbox" 
                                                   name="services[{{ $loop->index }}][selected]" 
                                                   value="1" 
                                                   id="check-{{ $service->id }}"
                                                   data-service-id="{{ $service->id }}"
                                                   data-name="{{ $service->name }}"
                                                   data-price-item="{{ $service->price_per_item }}"
                                                   data-price-kg="{{ $service->price_per_kg }}">
                                            <input type="hidden" name="services[{{ $loop->index }}][service_id]" value="{{ $service->id }}">
                                        </div>
                                        
                                        <!-- Service Info -->
                                        <div class="flex-grow-1">
                                            <label for="check-{{ $service->id }}" class="fw-bold text-dark fs-5 cursor-pointer d-block">{{ $service->name }}</label>
                                            <p class="text-secondary small mb-3">{{ $service->description }}</p>
                                            
                                            <!-- Pricing details -->
                                            <div class="d-flex gap-3 text-muted small mb-3">
                                                @if($service->price_per_kg > 0)
                                                    <span><i class="fa-solid fa-weight-scale me-1 text-primary"></i> ${{ number_format($service->price_per_kg, 2) }}/kg</span>
                                                @endif
                                                @if($service->price_per_item > 0)
                                                    <span><i class="fa-solid fa-shirt me-1 text-primary"></i> ${{ number_format($service->price_per_item, 2) }}/item</span>
                                                @endif
                                            </div>

                                            <!-- Dynamic inputs (Hidden until checked) -->
                                            <div class="row g-3 d-none" id="inputs-{{ $service->id }}">
                                                <!-- Quantity Input -->
                                                <div class="col-12 col-sm-4">
                                                    <label class="form-label fw-semibold small text-muted">Quantity</label>
                                                    <input type="number" name="services[{{ $loop->index }}][quantity]" value="1" min="1" class="form-control qty-input">
                                                </div>
                                                
                                                <!-- Weight Input -->
                                                @if($service->price_per_kg > 0)
                                                    <div class="col-12 col-sm-4">
                                                        <label class="form-label fw-semibold small text-muted">Weight (KG)</label>
                                                        <input type="number" step="0.01" min="0.1" name="services[{{ $loop->index }}][weight_kg]" class="form-control weight-input" placeholder="Optional">
                                                    </div>
                                                @endif

                                                <!-- Care Instructions -->
                                                <div class="col-12 col-sm-12">
                                                    <label class="form-label fw-semibold small text-muted">Care Instructions / Notes</label>
                                                    <input type="text" name="services[{{ $loop->index }}][care_instructions]" class="form-control notes-input" placeholder="e.g. Wash cold, hang dry">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Subtotal for this service -->
                                        <div class="text-end d-none" id="subtotal-container-{{ $service->id }}">
                                            <span class="text-muted small d-block">Subtotal</span>
                                            <span class="fs-4 fw-bold text-primary service-subtotal" id="subtotal-val-{{ $service->id }}">$0.00</span>
                                        </div>
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
                        <label for="pickup_address" class="form-label fw-semibold">Pickup Address</label>
                        <textarea name="pickup_address" id="pickup_address" rows="3" class="form-control @error('pickup_address') is-invalid @enderror" required>{{ old('pickup_address', $user->address) }}</textarea>
                        @error('pickup_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Delivery Address -->
                    <div class="col-12">
                        <label for="delivery_address" class="form-label fw-semibold">Delivery Address</label>
                        <textarea name="delivery_address" id="delivery_address" rows="3" class="form-control @error('delivery_address') is-invalid @enderror" required>{{ old('delivery_address', $user->address) }}</textarea>
                        @error('delivery_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pickup Time -->
                    <div class="col-12 col-md-6">
                        <label for="pickup_time" class="form-label fw-semibold">Preferred Pickup Time</label>
                        <input type="datetime-local" name="pickup_time" id="pickup_time" class="form-control @error('pickup_time') is-invalid @enderror" required>
                        @error('pickup_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Delivery Time -->
                    <div class="col-12 col-md-6">
                        <label for="delivery_time" class="form-label fw-semibold">Preferred Delivery Time</label>
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
                        <div class="card border border-2 rounded-4 payment-card cursor-pointer transition-all active border-primary bg-primary-subtle" id="pay-card-cash">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <input class="form-check-input payment-radio fs-4" type="radio" name="payment_method" id="pay-cash" value="cash" checked>
                                <div class="fs-2 text-primary"><i class="fa-solid fa-money-bill-wave"></i></div>
                                <div>
                                    <label class="fw-bold text-dark fs-5 mb-0 cursor-pointer d-block" for="pay-cash">Cash on Delivery</label>
                                    <small class="text-secondary">Pay when your laundry is delivered</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Zaad Mobile Money -->
                    <div class="col-12">
                        <div class="card border border-2 rounded-4 payment-card cursor-pointer transition-all" id="pay-card-zaad">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <input class="form-check-input payment-radio fs-4" type="radio" name="payment_method" id="pay-zaad" value="zaad">
                                <div class="fs-2 text-primary"><i class="fa-solid fa-mobile-screen-button"></i></div>
                                <div>
                                    <label class="fw-bold text-dark fs-5 mb-0 cursor-pointer d-block" for="pay-zaad">Zaad Mobile Money</label>
                                    <small class="text-secondary">Pay via Zaad mobile transfer</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edahab Mobile Money -->
                    <div class="col-12">
                        <div class="card border border-2 rounded-4 payment-card cursor-pointer transition-all" id="pay-card-edahab">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <input class="form-check-input payment-radio fs-4" type="radio" name="payment_method" id="pay-edahab" value="edahab">
                                <div class="fs-2 text-primary"><i class="fa-solid fa-mobile-screen-button"></i></div>
                                <div>
                                    <label class="fw-bold text-dark fs-5 mb-0 cursor-pointer d-block" for="pay-edahab">Edahab Mobile Money</label>
                                    <small class="text-secondary">Pay via Edahab mobile transfer</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment phone container -->
                <div class="mb-3 d-none" id="phone-input-container">
                    <label for="payment_phone" class="form-label fw-bold">Mobile Wallet Phone Number</label>
                    <input type="text" name="payment_phone" id="payment_phone" class="form-control" placeholder="e.g. +25261XXXXXX">
                    <div class="form-text text-muted small">Enter the registered phone number to receive a payment prompt.</div>
                </div>
            </div>

            <!-- STEP 4: CONFIRMATION -->
            <div class="step-panel bg-white p-4 rounded-4 shadow-sm d-none" id="step-4-panel">
                <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-clipboard-check text-primary me-2"></i>Step 4: Confirm Order</h4>
                
                <div class="alert alert-info border-0 rounded-3 mb-4">
                    Please review your order details below. Click <strong>Confirm Order</strong> at the bottom to finalize.
                </div>

                <!-- Selected Services Summary -->
                <div class="mb-4">
                    <h6 class="fw-bold text-muted small text-uppercase mb-2">Selected Services</h6>
                    <div class="list-group list-group-flush rounded-3" id="confirm-services-list">
                        <!-- Dynamic items -->
                    </div>
                </div>

                <!-- Scheduling Summary -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-1">Pickup Scheduled</h6>
                        <p class="mb-0 text-dark" id="confirm-pickup-time"></p>
                        <small class="text-muted" id="confirm-pickup-addr"></small>
                    </div>
                    <div class="col-12 col-sm-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-1">Delivery Scheduled</h6>
                        <p class="mb-0 text-dark" id="confirm-delivery-time"></p>
                        <small class="text-muted" id="confirm-delivery-addr"></small>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-1">Payment Method</h6>
                        <p class="mb-0 text-dark text-capitalize" id="confirm-payment-method"></p>
                        <small class="text-muted d-none" id="confirm-payment-phone-container">Phone: <span id="confirm-payment-phone"></span></small>
                    </div>
                    <div class="col-12 col-sm-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-1">Special Instructions</h6>
                        <p class="mb-0 text-dark small" id="confirm-special-instr"></p>
                    </div>
                </div>
            </div>

            <!-- Navigation Controls -->
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary fw-bold px-4 py-2 rounded-3 d-none" id="btn-back">Back</button>
                <div class="ms-auto"></div>
                <button type="button" class="btn btn-primary fw-bold px-4 py-2 rounded-3" id="btn-next">Next Step</button>
                <button type="submit" class="btn btn-success fw-bold px-4 py-2 rounded-3 d-none" id="btn-confirm">Confirm Order</button>
            </div>
        </div>

        <!-- Sticky Right Summary Panel -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white sticky-top" style="top: 100px; z-index: 1;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold text-dark mb-0">Order Summary</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="d-flex flex-column gap-3 mb-4" id="summary-items-list">
                        <div class="text-center py-4 text-muted small" id="summary-empty-msg">
                            No services selected.
                        </div>
                    </div>
                    <hr class="text-secondary opacity-25 my-3">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-semibold">Grand Total:</span>
                        <span class="fs-3 fw-bold text-primary" id="summary-grand-total">$0.00</span>
                    </div>

                    <div class="bg-light p-3 rounded-3 d-none" id="summary-payment-panel">
                        <span class="text-muted small d-block mb-1">Payment Channel</span>
                        <div class="fw-bold text-capitalize text-dark" id="summary-payment-val">Cash on Delivery</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let currentStep = 1;
        const totalSteps = 4;
        
        // DOM Elements
        const form = document.getElementById('multiStepOrderForm');
        const nextBtn = document.getElementById('btn-next');
        const backBtn = document.getElementById('btn-back');
        const confirmBtn = document.getElementById('btn-confirm');
        const phoneInputContainer = document.getElementById('phone-input-container');
        
        // Set min values for datetime local inputs
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(9, 0, 0, 0); // Default to 9:00 AM tomorrow
        const formattedTomorrow = tomorrow.toISOString().slice(0, 16);
        
        const pickupTimeInput = document.getElementById('pickup_time');
        const deliveryTimeInput = document.getElementById('delivery_time');
        
        pickupTimeInput.min = formattedTomorrow;
        pickupTimeInput.value = formattedTomorrow;
        
        // Delivery date min set to pickup time + 2 hours minimum
        pickupTimeInput.addEventListener('change', function() {
            if (this.value) {
                const pickupDate = new Date(this.value);
                pickupDate.setHours(pickupDate.getHours() + 2);
                deliveryTimeInput.min = pickupDate.toISOString().slice(0, 16);
                deliveryTimeInput.value = pickupDate.toISOString().slice(0, 16);
            }
        });
        
        // Initial trigger
        pickupTimeInput.dispatchEvent(new Event('change'));

        // Step transition logic
        function showStep(step) {
            document.querySelectorAll('.step-panel').forEach(p => p.classList.add('d-none'));
            document.getElementById(`step-${step}-panel`).classList.remove('d-none');
            
            // Update Indicators
            for (let i = 1; i <= totalSteps; i++) {
                const ind = document.getElementById(`indicator-${i}`);
                const badge = ind.querySelector('.badge');
                if (i === step) {
                    ind.classList.remove('text-muted');
                    ind.classList.add('text-primary', 'fw-bold');
                    badge.classList.remove('bg-secondary');
                    badge.classList.add('bg-primary');
                } else if (i < step) {
                    ind.classList.remove('text-muted');
                    ind.classList.add('text-success');
                    badge.classList.remove('bg-secondary', 'bg-primary');
                    badge.classList.add('bg-success');
                } else {
                    ind.classList.remove('text-primary', 'text-success', 'fw-bold');
                    ind.classList.add('text-muted');
                    badge.classList.remove('bg-primary', 'bg-success');
                    badge.classList.add('bg-secondary');
                }
            }

            // Buttons controls
            if (step === 1) {
                backBtn.classList.add('d-none');
                nextBtn.classList.remove('d-none');
                confirmBtn.classList.add('d-none');
            } else if (step === totalSteps) {
                backBtn.classList.remove('d-none');
                nextBtn.classList.add('d-none');
                confirmBtn.classList.remove('d-none');
                compileStep4Summary();
            } else {
                backBtn.classList.remove('d-none');
                nextBtn.classList.remove('d-none');
                confirmBtn.classList.add('d-none');
            }
        }

        // Live calculation logic
        function updatePrices() {
            let grandTotal = 0;
            const summaryList = document.getElementById('summary-items-list');
            summaryList.innerHTML = '';
            
            const checkboxes = document.querySelectorAll('.service-checkbox:checked');
            
            if (checkboxes.length === 0) {
                document.getElementById('summary-empty-msg').style.display = 'block';
                summaryList.appendChild(document.getElementById('summary-empty-msg'));
                document.getElementById('summary-grand-total').textContent = '$0.00';
                return;
            }
            
            document.getElementById('summary-empty-msg').style.display = 'none';

            checkboxes.forEach(function (chk) {
                const serviceId = chk.dataset.serviceId;
                const name = chk.dataset.name;
                const priceItem = parseFloat(chk.dataset.priceItem);
                const priceKg = parseFloat(chk.dataset.priceKg);
                
                const card = document.getElementById(`card-${serviceId}`);
                const inputsDiv = document.getElementById(`inputs-${serviceId}`);
                const subtotalContainer = document.getElementById(`subtotal-container-${serviceId}`);
                
                inputsDiv.classList.remove('d-none');
                subtotalContainer.classList.remove('d-none');
                card.classList.add('border-primary', 'bg-light');
                
                // Get qty and weight values
                const qtyInput = inputsDiv.querySelector('.qty-input');
                const weightInput = inputsDiv.querySelector('.weight-input');
                
                const qty = parseInt(qtyInput.value) || 1;
                const weight = parseFloat(weightInput ? weightInput.value : 0) || 0;
                
                let subtotal = 0;
                let detailsText = '';
                
                if (weight > 0 && priceKg > 0) {
                    subtotal = weight * priceKg;
                    detailsText = `${weight.toFixed(2)} kg × $${priceKg.toFixed(2)}`;
                } else {
                    subtotal = qty * priceItem;
                    detailsText = `${qty} items × $${priceItem.toFixed(2)}`;
                }
                
                grandTotal += subtotal;
                
                // Update subtotal display in Step 1
                document.getElementById(`subtotal-val-${serviceId}`).textContent = `$${subtotal.toFixed(2)}`;
                
                // Append to sidebar summary
                const itemDiv = document.createElement('div');
                itemDiv.className = 'd-flex justify-content-between align-items-start small';
                itemDiv.innerHTML = `
                    <div>
                        <div class="fw-bold text-dark">${name}</div>
                        <span class="text-muted fs-9">${detailsText}</span>
                    </div>
                    <span class="fw-bold text-dark">$${subtotal.toFixed(2)}</span>
                `;
                summaryList.appendChild(itemDiv);
            });
            
            document.getElementById('summary-grand-total').textContent = `$${grandTotal.toFixed(2)}`;
        }

        // Event listeners for Step 1 selection and inputs
        document.querySelectorAll('.service-checkbox').forEach(function (chk) {
            chk.addEventListener('change', function () {
                const serviceId = this.dataset.serviceId;
                const card = document.getElementById(`card-${serviceId}`);
                const inputsDiv = document.getElementById(`inputs-${serviceId}`);
                const subtotalContainer = document.getElementById(`subtotal-container-${serviceId}`);
                
                if (this.checked) {
                    inputsDiv.classList.remove('d-none');
                    subtotalContainer.classList.remove('d-none');
                    card.classList.add('border-primary', 'bg-light');
                } else {
                    inputsDiv.classList.add('d-none');
                    subtotalContainer.classList.add('d-none');
                    card.classList.remove('border-primary', 'bg-light');
                }
                updatePrices();
            });
        });

        document.querySelectorAll('.qty-input, .weight-input').forEach(function (input) {
            input.addEventListener('input', updatePrices);
            input.addEventListener('change', updatePrices);
        });

        // Step 3 Payment card selection listeners
        document.querySelectorAll('.payment-card').forEach(function (card) {
            card.addEventListener('click', function () {
                document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('active', 'border-primary', 'bg-primary-subtle'));
                this.classList.add('active', 'border-primary', 'bg-primary-subtle');
                
                const radio = this.querySelector('.payment-radio');
                radio.checked = true;
                
                // Show/hide phone inputs
                if (radio.value === 'zaad' || radio.value === 'edahab') {
                    phoneInputContainer.classList.remove('d-none');
                    document.getElementById('payment_phone').required = true;
                } else {
                    phoneInputContainer.classList.add('d-none');
                    document.getElementById('payment_phone').required = false;
                }
                
                // Update side panel payment
                document.getElementById('summary-payment-panel').classList.remove('d-none');
                document.getElementById('summary-payment-val').textContent = radio.value === 'cash' ? 'Cash on Delivery' : radio.value.toUpperCase();
            });
        });
        
        // Trigger initial payment display
        document.getElementById('pay-card-cash').dispatchEvent(new Event('click'));

        // Wizard navigation buttons actions
        nextBtn.addEventListener('click', function () {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        });

        backBtn.addEventListener('click', function () {
            currentStep--;
            showStep(currentStep);
        });

        // Client-side validations
        function validateStep(step) {
            if (step === 1) {
                const checked = document.querySelectorAll('.service-checkbox:checked');
                if (checked.length === 0) {
                    alert('Please select at least one laundry service to continue.');
                    return false;
                }
                
                // Ensure quantities are set
                let valid = true;
                checked.forEach(chk => {
                    const id = chk.dataset.serviceId;
                    const qty = document.getElementById(`inputs-${id}`).querySelector('.qty-input').value;
                    if (parseInt(qty) < 1) {
                        alert('Quantity must be 1 or greater.');
                        valid = false;
                    }
                });
                return valid;
            }
            
            if (step === 2) {
                const pickupAddr = document.getElementById('pickup_address').value.trim();
                const deliveryAddr = document.getElementById('delivery_address').value.trim();
                const pickupTime = document.getElementById('pickup_time').value;
                const deliveryTime = document.getElementById('delivery_time').value;
                
                if (!pickupAddr || !deliveryAddr || !pickupTime || !deliveryTime) {
                    alert('Please fill out all address and scheduling fields.');
                    return false;
                }
                
                if (new Date(deliveryTime) <= new Date(pickupTime)) {
                    alert('Delivery time must be scheduled after the pickup time.');
                    return false;
                }
                return true;
            }

            if (step === 3) {
                const method = document.querySelector('input[name="payment_method"]:checked').value;
                if (method === 'zaad' || method === 'edahab') {
                    const phone = document.getElementById('payment_phone').value.trim();
                    if (!phone) {
                        alert('Please enter your mobile money phone number.');
                        return false;
                    }
                }
                return true;
            }
            return true;
        }

        // Compile Summary details for Step 4 review
        function compileStep4Summary() {
            const list = document.getElementById('confirm-services-list');
            list.innerHTML = '';
            
            const checked = document.querySelectorAll('.service-checkbox:checked');
            checked.forEach(function (chk) {
                const serviceId = chk.dataset.serviceId;
                const name = chk.dataset.name;
                const priceItem = parseFloat(chk.dataset.priceItem);
                const priceKg = parseFloat(chk.dataset.priceKg);
                
                const inputsDiv = document.getElementById(`inputs-${serviceId}`);
                const qty = inputsDiv.querySelector('.qty-input').value;
                const weight = inputsDiv.querySelector('.weight-input') ? inputsDiv.querySelector('.weight-input').value : '';
                const notes = inputsDiv.querySelector('.notes-input').value;
                
                let subtotal = 0;
                let qtyText = '';
                if (weight && parseFloat(weight) > 0) {
                    subtotal = parseFloat(weight) * priceKg;
                    qtyText = `Weight: ${weight} kg (Pricing per KG)`;
                } else {
                    subtotal = parseInt(qty) * priceItem;
                    qtyText = `Qty: ${qty} items (Pricing per Item)`;
                }
                
                const li = document.createElement('div');
                li.className = 'list-group-item d-flex justify-content-between align-items-center py-3 border-bottom';
                li.innerHTML = `
                    <div>
                        <div class="fw-bold text-dark">${name}</div>
                        <span class="text-muted small">${qtyText}</span>
                        ${notes ? `<div class="small text-warning mt-1"><i class="fa-regular fa-comment-dots"></i> ${notes}</div>` : ''}
                    </div>
                    <span class="fw-bold text-primary">$${subtotal.toFixed(2)}</span>
                `;
                list.appendChild(li);
            });

            // Timings & Addresses
            document.getElementById('confirm-pickup-time').textContent = new Date(pickupTimeInput.value).toLocaleString();
            document.getElementById('confirm-pickup-addr').textContent = document.getElementById('pickup_address').value;
            document.getElementById('confirm-delivery-time').textContent = new Date(deliveryTimeInput.value).toLocaleString();
            document.getElementById('confirm-delivery-addr').textContent = document.getElementById('delivery_address').value;

            // Payment method
            const method = document.querySelector('input[name="payment_method"]:checked').value;
            document.getElementById('confirm-payment-method').textContent = method === 'cash' ? 'Cash on Delivery' : method.toUpperCase();
            
            const phoneVal = document.getElementById('payment_phone').value.trim();
            if (method !== 'cash' && phoneVal) {
                document.getElementById('confirm-payment-phone-container').classList.remove('d-none');
                document.getElementById('confirm-payment-phone').textContent = phoneVal;
            } else {
                document.getElementById('confirm-payment-phone-container').classList.add('d-none');
            }

            // Instructions
            const instr = document.getElementById('special_instructions').value.trim();
            document.getElementById('confirm-special-instr').textContent = instr ? instr : 'None';
        }
    });
</script>
@endsection
