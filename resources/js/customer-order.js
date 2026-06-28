/* customer-order.js */

document.addEventListener('DOMContentLoaded', () => {
    // Cache DOM Elements
    const dom = {
        form: document.getElementById('multiStepOrderForm'),
        steps: document.querySelectorAll('.step-panel'),
        indicators: document.querySelectorAll('.step-indicator-item'),
        progressBarFill: document.getElementById('wizardProgressFill'),
        progressBarText: document.getElementById('wizardProgressText'),
        
        // Navigation Buttons
        btnBack: document.getElementById('btn-back'),
        btnNext: document.getElementById('btn-next'),
        btnConfirm: document.getElementById('btn-confirm'),
        cashNextBtn: document.getElementById('cashNextBtn'),
        iHavePaidBtn: document.getElementById('iHavePaidBtn'),
        
        // Inputs & Panels
        servicesContainer: document.getElementById('step-1-panel'),
        pickupAddress: document.getElementById('pickup_address'),
        deliveryAddress: document.getElementById('delivery_address'),
        pickupTime: document.getElementById('pickup_time'),
        deliveryTime: document.getElementById('delivery_time'),
        specialInstructions: document.getElementById('special_instructions'),
        
        // Payment
        paymentCards: document.querySelectorAll('.payment-option-card'),
        paymentPhone: document.getElementById('payment_phone'),
        phoneInputContainer: document.getElementById('phone-input-container'),
        mobileInstructions: document.getElementById('mobilePayInstructions'),
        mobileProofFields: document.getElementById('mobilePayProofFields'),
        merchantNumber: document.getElementById('merchantNumberDisplay'),
        walletPhoneInput: document.getElementById('walletPhoneInput'),
        senderNameInput: document.getElementById('senderNameInput'),
        confirmedInput: document.getElementById('customerPaymentConfirmed'),
        
        // Sidebar Summary
        sidebarColumn: document.getElementById('orderSidebarColumn'),
        stepsColumn: document.getElementById('stepsColumn'),
        summaryServicesList: document.getElementById('summaryServicesList'),
        summaryGrandTotal: document.getElementById('summaryGrandTotal'),
        summaryPaymentRow: document.getElementById('summaryPaymentRow'),
        summaryPaymentLabel: document.getElementById('summaryPaymentLabel'),
        summaryPickupAddress: document.getElementById('summaryPickupAddress'),
        summaryDeliveryAddress: document.getElementById('summaryDeliveryAddress'),
        summaryEstCompletion: document.getElementById('summaryEstCompletion'),
        
        // Step 4 Confirmation Summaries
        confirmServicesList: document.getElementById('confirm-services-list'),
        confirmPickupTime: document.getElementById('confirm-pickup-time'),
        confirmPickupAddr: document.getElementById('confirm-pickup-addr'),
        confirmDeliveryTime: document.getElementById('confirm-delivery-time'),
        confirmDeliveryAddr: document.getElementById('confirm-delivery-addr'),
        confirmPaymentMethod: document.getElementById('confirm-payment-method'),
        confirmPaymentPhoneContainer: document.getElementById('confirm-payment-phone-container'),
        confirmPaymentPhone: document.getElementById('confirm-payment-phone'),
        confirmSpecialInstr: document.getElementById('confirm-special-instr'),
        confirmEstCompletion: document.getElementById('confirm-est-completion')
    };

    // Wizard State Management
    const state = {
        currentStep: 1,
        totalSteps: 4,
        selectedPaymentMethod: 'cash',
        selectedPaymentLabel: 'Cash on Delivery',
        grandTotal: 0
    };

    // Helper functions
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    };

    const formatDate = (dateStr) => {
        if (!dateStr) return 'Not scheduled';
        const date = new Date(dateStr);
        return date.toLocaleString('en-US', {
            dateStyle: 'medium',
            timeStyle: 'short'
        });
    };

    // Core Wizard Functions
    const init = () => {
        setupTimeLimits();
        registerEvents();
        showStep(1);
        calculateOrder();
    };

    const setupTimeLimits = () => {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(9, 0, 0, 0); // Default to 9:00 AM tomorrow
        const formattedTomorrow = tomorrow.toISOString().slice(0, 16);
        
        if (dom.pickupTime) {
            dom.pickupTime.min = formattedTomorrow;
            dom.pickupTime.value = formattedTomorrow;
        }
        
        // Delivery date min set to pickup time + 2 hours minimum
        if (dom.pickupTime && dom.deliveryTime) {
            dom.pickupTime.addEventListener('change', () => {
                if (dom.pickupTime.value) {
                    const pickupDate = new Date(dom.pickupTime.value);
                    pickupDate.setHours(pickupDate.getHours() + 2);
                    dom.deliveryTime.min = pickupDate.toISOString().slice(0, 16);
                    dom.deliveryTime.value = pickupDate.toISOString().slice(0, 16);
                }
            });
            dom.pickupTime.dispatchEvent(new Event('change'));
        }
    };

    const registerEvents = () => {
        // Navigation clicks
        if (dom.btnNext) dom.btnNext.addEventListener('click', () => nextStep());
        if (dom.btnBack) dom.btnBack.addEventListener('click', () => previousStep());
        
        // Address & Time text updates inside sidebar
        if (dom.pickupAddress) dom.pickupAddress.addEventListener('input', () => updateSummary());
        if (dom.deliveryAddress) dom.deliveryAddress.addEventListener('input', () => updateSummary());
        if (dom.deliveryTime) dom.deliveryTime.addEventListener('change', () => updateSummary());

        // Service check/input changes
        if (dom.servicesContainer) {
            dom.servicesContainer.addEventListener('change', (e) => {
                if (e.target.classList.contains('service-checkbox')) {
                    toggleServiceInputs(e.target);
                }
                calculateOrder();
            });
            
            dom.servicesContainer.addEventListener('input', (e) => {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('weight-input')) {
                    calculateOrder();
                }
            });
        }

        // Payment option clicks
        dom.paymentCards.forEach(card => {
            card.addEventListener('click', () => selectPayment(card));
        });

        // Form Submit
        if (dom.form) {
            dom.form.addEventListener('submit', (e) => {
                populateHiddenFields();
            });
        }
        
        // Setup global references for onclick wizard attributes
        window.goToStep = (step) => goToStep(step);
        window.handleIHavePaid = () => handleIHavePaid();
    };

    const showStep = (step) => {
        // Hide all step panels and show target panel
        dom.steps.forEach(p => p.classList.add('d-none'));
        const activePanel = document.getElementById(`step-${step}-panel`);
        if (activePanel) activePanel.classList.remove('d-none');
        
        // Update state
        state.currentStep = step;

        // Update Indicators
        dom.indicators.forEach((indicator, index) => {
            const indStep = index + 1;
            indicator.classList.remove('active', 'completed');
            
            const stepNum = indicator.querySelector('.step-num');
            const checkIcon = indicator.querySelector('.fa-check');
            
            if (indStep === step) {
                indicator.classList.add('active');
                if (stepNum) stepNum.classList.remove('d-none');
                if (checkIcon) checkIcon.classList.add('d-none');
            } else if (indStep < step) {
                indicator.classList.add('completed');
                if (stepNum) stepNum.classList.add('d-none');
                if (checkIcon) checkIcon.classList.remove('d-none');
            } else {
                if (stepNum) stepNum.classList.remove('d-none');
                if (checkIcon) checkIcon.classList.add('d-none');
            }
        });

        // Update Progress Bar
        updateProgress();

        // Control Wizard Buttons
        if (step === 1) {
            dom.btnBack.classList.add('d-none');
            dom.btnNext.classList.remove('d-none');
            dom.btnConfirm.classList.add('d-none');
            if (dom.cashNextBtn) dom.cashNextBtn.style.display = 'none';
            if (dom.iHavePaidBtn) dom.iHavePaidBtn.style.display = 'none';
        } else if (step === 2) {
            dom.btnBack.classList.remove('d-none');
            dom.btnNext.classList.remove('d-none');
            dom.btnConfirm.classList.add('d-none');
            if (dom.cashNextBtn) dom.cashNextBtn.style.display = 'none';
            if (dom.iHavePaidBtn) dom.iHavePaidBtn.style.display = 'none';
        } else if (step === 3) {
            dom.btnBack.classList.remove('d-none');
            dom.btnNext.classList.add('d-none'); // Hide generic Next button
            dom.btnConfirm.classList.add('d-none');
            
            // Show custom buttons depending on selection
            if (state.selectedPaymentMethod === 'zaad' || state.selectedPaymentMethod === 'edahab') {
                if (dom.cashNextBtn) dom.cashNextBtn.style.display = 'none';
                if (dom.iHavePaidBtn) dom.iHavePaidBtn.style.display = 'inline-block';
            } else {
                if (dom.cashNextBtn) dom.cashNextBtn.style.display = 'inline-block';
                if (dom.iHavePaidBtn) dom.iHavePaidBtn.style.display = 'none';
            }
        } else if (step === state.totalSteps) {
            dom.btnBack.classList.remove('d-none');
            dom.btnNext.classList.add('d-none');
            dom.btnConfirm.classList.remove('d-none');
            if (dom.cashNextBtn) dom.cashNextBtn.style.display = 'none';
            if (dom.iHavePaidBtn) dom.iHavePaidBtn.style.display = 'none';
            compileStep4Summary();
        }

        // Sidebar display logic
        if (step >= 3) {
            dom.sidebarColumn.style.display = 'block';
            dom.stepsColumn.className = 'col-12 col-md-8';
            updateSummary();
            dom.summaryPaymentRow.style.display = (step === 4) ? 'block' : 'none';
        } else {
            dom.sidebarColumn.style.display = 'none';
            dom.stepsColumn.className = 'col-12';
        }
    };

    const nextStep = () => {
        if (validateCurrentStep(state.currentStep)) {
            showStep(state.currentStep + 1);
        }
    };

    const previousStep = () => {
        showStep(state.currentStep - 1);
    };

    const goToStep = (step) => {
        if (step > state.currentStep) {
            if (!validateCurrentStep(state.currentStep)) {
                return;
            }
        }
        showStep(step);
    };

    const updateProgress = () => {
        const percent = Math.round(((state.currentStep - 1) / (state.totalSteps - 1)) * 100);
        if (dom.progressBarFill) dom.progressBarFill.style.width = `${percent}%`;
        if (dom.progressBarText) {
            dom.progressBarText.textContent = `Step ${state.currentStep} of ${state.totalSteps} (${percent}% Complete)`;
        }
    };

    // Calculate Prices & Cart
    const toggleServiceInputs = (checkbox) => {
        const id = checkbox.dataset.serviceId;
        const card = document.getElementById(`card-${id}`);
        const inputsDiv = document.getElementById(`inputs-${id}`);
        const subtotalContainer = document.getElementById(`subtotal-container-${id}`);
        
        if (checkbox.checked) {
            if (inputsDiv) inputsDiv.classList.remove('d-none');
            if (subtotalContainer) subtotalContainer.classList.remove('d-none');
            if (card) card.classList.add('selected');
        } else {
            if (inputsDiv) inputsDiv.classList.add('d-none');
            if (subtotalContainer) subtotalContainer.classList.add('d-none');
            if (card) card.classList.remove('selected');
        }
    };

    const calculateOrder = () => {
        const checkboxes = document.querySelectorAll('.service-checkbox:checked');
        let total = 0;

        checkboxes.forEach(chk => {
            const serviceId = chk.dataset.serviceId;
            const priceItem = parseFloat(chk.dataset.priceItem);
            const priceKg = parseFloat(chk.dataset.priceKg);
            
            const inputsDiv = document.getElementById(`inputs-${serviceId}`);
            if (!inputsDiv) return;

            const qtyInput = inputsDiv.querySelector('.qty-input');
            const weightInput = inputsDiv.querySelector('.weight-input');
            
            const qty = parseInt(qtyInput ? qtyInput.value : 1) || 1;
            const weight = parseFloat(weightInput ? weightInput.value : 0) || 0;
            
            let subtotal = 0;
            if (weight > 0 && priceKg > 0) {
                subtotal = weight * priceKg;
            } else {
                subtotal = qty * priceItem;
            }
            
            total += subtotal;
            
            const subtotalValEl = document.getElementById(`subtotal-val-${serviceId}`);
            if (subtotalValEl) {
                subtotalValEl.textContent = formatCurrency(subtotal);
            }
        });

        state.grandTotal = total;
        updateSummary();
    };

    // Update Sidebar
    const updateSummary = () => {
        if (!dom.summaryServicesList) return;

        const checked = document.querySelectorAll('.service-checkbox:checked');

        if (dom.summaryPickupAddress) {
            dom.summaryPickupAddress.textContent = dom.pickupAddress.value.trim() || 'Not provided';
        }
        if (dom.summaryDeliveryAddress) {
            dom.summaryDeliveryAddress.textContent = dom.deliveryAddress.value.trim() || 'Not provided';
        }
        if (dom.summaryEstCompletion) {
            dom.summaryEstCompletion.textContent = formatDate(dom.deliveryTime.value);
        }

        if (checked.length === 0) {
            dom.summaryServicesList.innerHTML = `
                <div class="text-center py-4 text-muted small" id="summary-empty-msg">
                    No services selected.
                </div>`;
            if (dom.summaryGrandTotal) dom.summaryGrandTotal.textContent = formatCurrency(0);
            return;
        }

        let html = '';
        checked.forEach(checkbox => {
            const serviceId = checkbox.dataset.serviceId;
            const card = document.getElementById(`card-${serviceId}`);
            if (!card) return;

            const serviceName = card.dataset.serviceName;
            const pricePerKg = parseFloat(card.dataset.pricePerKg) || 0;
            const pricePerItem = parseFloat(card.dataset.pricePerItem) || 0;
            
            const inputsDiv = document.getElementById(`inputs-${serviceId}`);
            const qty = parseFloat(inputsDiv.querySelector('.service-qty')?.value) || 0;
            const weight = parseFloat(inputsDiv.querySelector('.service-weight')?.value) || 0;
            
            let subtotal = 0;
            if (weight > 0 && pricePerKg > 0) {
                subtotal = weight * pricePerKg;
            } else {
                subtotal = qty * pricePerItem;
            }

            html += `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="fw-semibold small">${serviceName}</div>
                        <div class="text-muted" style="font-size:0.75rem;">
                            ${qty > 0 ? 'Qty: ' + qty : ''}
                            ${weight > 0 ? (qty > 0 ? ' · ' : '') + weight + ' kg' : ''}
                        </div>
                    </div>
                    <span class="text-primary small fw-semibold">${formatCurrency(subtotal)}</span>
                </div>`;
        });

        dom.summaryServicesList.innerHTML = html;
        if (dom.summaryGrandTotal) dom.summaryGrandTotal.textContent = formatCurrency(state.grandTotal);
        if (dom.summaryPaymentLabel) dom.summaryPaymentLabel.textContent = state.selectedPaymentLabel;
    };

    // Payment method selection
    const selectPayment = (card) => {
        dom.paymentCards.forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        
        const radio = card.querySelector('.payment-radio');
        if (radio) radio.checked = true;
        
        state.selectedPaymentMethod = card.dataset.payment;
        state.selectedPaymentLabel = card.dataset.label;
        
        // Show/hide merchant info & proof fields
        if (state.selectedPaymentMethod === 'zaad') {
            if (dom.merchantNumber) dom.merchantNumber.textContent = '252-61-4700000';
            if (dom.mobileInstructions) dom.mobileInstructions.style.display = 'block';
            if (dom.mobileProofFields) dom.mobileProofFields.style.display = 'block';
        } else if (state.selectedPaymentMethod === 'edahab') {
            if (dom.merchantNumber) dom.merchantNumber.textContent = '252-63-4700000';
            if (dom.mobileInstructions) dom.mobileInstructions.style.display = 'block';
            if (dom.mobileProofFields) dom.mobileProofFields.style.display = 'block';
        } else {
            if (dom.mobileInstructions) dom.mobileInstructions.style.display = 'none';
            if (dom.mobileProofFields) dom.mobileProofFields.style.display = 'none';
        }
        
        // Ensure standard container stays hidden (using custom proof fields instead)
        if (dom.phoneInputContainer) dom.phoneInputContainer.classList.add('d-none');
        
        // Refresh buttons and summaries
        showStep(state.currentStep);
    };

    const handleIHavePaid = () => {
        const walletPhone = dom.walletPhoneInput ? dom.walletPhoneInput.value.trim() : '';
        const senderName = dom.senderNameInput ? dom.senderNameInput.value.trim() : '';

        if (!walletPhone || !senderName) {
            alert('Please enter your Wallet Phone Number and Sender Name before confirming payment.');
            return;
        }

        // Set customer payment confirmed hidden field
        if (dom.confirmedInput) {
            dom.confirmedInput.value = '1';
        }

        goToStep(4);
    };

    // Form Submission Preparation
    const populateHiddenFields = () => {
        const walletPhone = dom.walletPhoneInput ? dom.walletPhoneInput.value.trim() : '';
        const senderName = dom.senderNameInput ? dom.senderNameInput.value.trim() : '';
        
        addHiddenInput('wallet_phone', walletPhone);
        addHiddenInput('sender_name', senderName);
        
        // Mirror phone input to payment_phone
        if (state.selectedPaymentMethod === 'zaad' || state.selectedPaymentMethod === 'edahab') {
            if (dom.paymentPhone) dom.paymentPhone.value = walletPhone;
        }
        
        const confirmedVal = dom.confirmedInput ? dom.confirmedInput.value : '0';
        addHiddenInput('customer_payment_confirmed', confirmedVal);
    };

    const addHiddenInput = (name, value) => {
        let input = dom.form.querySelector(`input[name="${name}"]`);
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            dom.form.appendChild(input);
        }
        input.value = value;
    };

    // Validation
    const validateCurrentStep = (step) => {
        if (step === 1) {
            const checked = document.querySelectorAll('.service-checkbox:checked');
            if (checked.length === 0) {
                alert('Please select at least one laundry service to continue.');
                return false;
            }
            
            let valid = true;
            checked.forEach(chk => {
                const id = chk.dataset.serviceId;
                const qtyVal = document.getElementById(`inputs-${id}`).querySelector('.qty-input').value;
                if (parseInt(qtyVal) < 1) {
                    alert('Quantity must be 1 or greater.');
                    valid = false;
                }
            });
            return valid;
        }
        
        if (step === 2) {
            const pickupAddr = dom.pickupAddress.value.trim();
            const deliveryAddr = dom.deliveryAddress.value.trim();
            const pickupT = dom.pickupTime.value;
            const deliveryT = dom.deliveryTime.value;
            
            if (!pickupAddr || !deliveryAddr || !pickupT || !deliveryT) {
                alert('Please fill out all address and scheduling fields.');
                return false;
            }
            
            if (new Date(deliveryT) <= new Date(pickupT)) {
                alert('Delivery time must be scheduled after the pickup time.');
                return false;
            }
            return true;
        }

        if (step === 3) {
            const method = state.selectedPaymentMethod;
            if (method === 'zaad' || method === 'edahab') {
                const walletPhone = dom.walletPhoneInput ? dom.walletPhoneInput.value.trim() : '';
                const senderName = dom.senderNameInput ? dom.senderNameInput.value.trim() : '';
                if (!walletPhone || !senderName) {
                    alert('Please enter your Wallet Phone Number and Sender Name to continue.');
                    return false;
                }
                if (dom.paymentPhone) dom.paymentPhone.value = walletPhone;
            }
            return true;
        }

        return true;
    };

    // Step 4 Confirmation Compilation
    const compileStep4Summary = () => {
        if (!dom.confirmServicesList) return;

        dom.confirmServicesList.innerHTML = '';
        const checked = document.querySelectorAll('.service-checkbox:checked');
        
        checked.forEach(chk => {
            const serviceId = chk.dataset.serviceId;
            const card = document.getElementById(`card-${serviceId}`);
            if (!card) return;

            const name = card.dataset.serviceName;
            const priceItem = parseFloat(card.dataset.pricePerItem) || 0;
            const priceKg = parseFloat(card.dataset.pricePerKg) || 0;
            
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
            
            const itemDiv = document.createElement('div');
            itemDiv.className = 'list-group-item d-flex justify-content-between align-items-center py-3 border-bottom';
            itemDiv.innerHTML = `
                <div>
                    <div class="fw-bold text-dark">${name}</div>
                    <span class="text-muted small">${qtyText}</span>
                    ${notes ? `<div class="small text-warning mt-1"><i class="fa-regular fa-comment-dots"></i> ${notes}</div>` : ''}
                </div>
                <span class="fw-bold text-primary">${formatCurrency(subtotal)}</span>
            `;
            dom.confirmServicesList.appendChild(itemDiv);
        });

        // Address & Timings
        if (dom.confirmPickupTime) dom.confirmPickupTime.textContent = formatDate(dom.pickupTime.value);
        if (dom.confirmPickupAddr) dom.confirmPickupAddr.textContent = dom.pickupAddress.value;
        if (dom.confirmDeliveryTime) dom.confirmDeliveryTime.textContent = formatDate(dom.deliveryTime.value);
        if (dom.confirmDeliveryAddr) dom.confirmDeliveryAddr.textContent = dom.deliveryAddress.value;
        
        // Estimated Completion
        if (dom.confirmEstCompletion) dom.confirmEstCompletion.textContent = formatDate(dom.deliveryTime.value);

        // Payment Method
        if (dom.confirmPaymentMethod) {
            dom.confirmPaymentMethod.textContent = state.selectedPaymentMethod === 'cash' 
                ? 'Cash on Delivery' 
                : state.selectedPaymentLabel;
        }
        
        const walletPhone = dom.walletPhoneInput ? dom.walletPhoneInput.value.trim() : '';
        if (state.selectedPaymentMethod !== 'cash' && walletPhone) {
            if (dom.confirmPaymentPhoneContainer) dom.confirmPaymentPhoneContainer.classList.remove('d-none');
            if (dom.confirmPaymentPhone) dom.confirmPaymentPhone.textContent = walletPhone;
        } else {
            if (dom.confirmPaymentPhoneContainer) dom.confirmPaymentPhoneContainer.classList.add('d-none');
        }

        // Instructions
        const instr = dom.specialInstructions.value.trim();
        if (dom.confirmSpecialInstr) dom.confirmSpecialInstr.textContent = instr ? instr : 'None';
    };

    // Run Initialization
    init();
});
