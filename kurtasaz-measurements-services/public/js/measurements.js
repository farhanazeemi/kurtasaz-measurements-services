/**
 * KurtaSaz Sizing & Bespoke WooCommerce Form Handler
 */
document.addEventListener('DOMContentLoaded', function() {
	const container = document.getElementById('ksms-measurements-form-container');
	if (!container) return;

	// Locate parent WooCommerce Add to Cart form and submit button
	const wcCartForm = container.closest('form.cart') || document.querySelector('form.cart');
	const nativeSubmitBtn = wcCartForm ? wcCartForm.querySelector('.single_add_to_cart_button') : null;

	const prevBtn = document.getElementById('ksms-prev-btn');
	const nextBtn = document.getElementById('ksms-next-btn');
	const submitBtn = document.getElementById('ksms-submit-btn');
	const errorAlert = document.getElementById('ksms-form-error');
	const errorText = document.getElementById('ksms-error-text');

	// Status Overlay & Diagram Elements
	const statusOverlay = document.getElementById('ksms-status-overlay');
	const svgElement = document.getElementById('ksms-svg-body');
	const svgLabel = document.getElementById('svg-active-part-label');

	let currentStep = 1;
	const totalSteps = 5;

	/**
	 * Hide WooCommerce Native Add to Cart button during steps 1-4
	 */
	function manageNativeButtonVisibility() {
		if (nativeSubmitBtn) {
			if (currentStep < totalSteps) {
				nativeSubmitBtn.style.setProperty('display', 'none', 'important');
			} else {
				// Hide native button completely because our custom #ksms-submit-btn will trigger it.
				// This keeps the layout integrated with the multi-step form's footer navigation.
				nativeSubmitBtn.style.setProperty('display', 'none', 'important');
			}
		}
	}

	/**
	 * Initialize Step Content Visibility based on selected service
	 */
	function updateConditionalStep3Views() {
		const serviceTypeEl = container.querySelector('input[name="ksms_service_type"]:checked');
		if (!serviceTypeEl) return;
		
		const serviceType = serviceTypeEl.value;
		const unstitchedView = document.getElementById('sizing-unstitched-container');
		const stitchedView = document.getElementById('sizing-stitched-container');
		const customView = document.getElementById('sizing-custom-container');

		// Hide all
		unstitchedView.classList.add('hidden');
		stitchedView.classList.add('hidden');
		customView.classList.add('hidden');

		// Show selected
		if (serviceType === 'unstitched') {
			unstitchedView.classList.remove('hidden');
		} else if (serviceType === 'stitched') {
			stitchedView.classList.remove('hidden');
		} else if (serviceType === 'custom') {
			customView.classList.remove('hidden');
		}
	}

	// Attach change listeners to step 1 service radios
	const serviceRadios = container.querySelectorAll('input[name="ksms_service_type"]');
	serviceRadios.forEach(radio => {
		radio.addEventListener('change', updateConditionalStep3Views);
	});

	// Call once initially
	updateConditionalStep3Views();
	manageNativeButtonVisibility();

	/**
	 * Dynamic Step Indicator Updates
	 */
	function updateStepIndicators() {
		const indicators = container.querySelectorAll('.step-indicator');
		indicators.forEach(indicator => {
			const stepNum = parseInt(indicator.getAttribute('data-step'), 10);
			const numCircle = indicator.querySelector('div');
			const label = indicator.querySelector('span');

			if (stepNum < currentStep) {
				numCircle.className = 'w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs bg-emerald-500 text-slate-950 transition-colors duration-300';
				numCircle.innerHTML = '✓';
				if (label) label.className = 'mt-2 text-2xs font-semibold text-emerald-400 tracking-wider hidden sm:inline';
			} else if (stepNum === currentStep) {
				numCircle.className = 'w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs bg-amber-500 text-slate-950 transition-colors duration-300';
				numCircle.innerHTML = stepNum;
				if (label) label.className = 'mt-2 text-2xs font-semibold text-amber-400 tracking-wider hidden sm:inline';
			} else {
				numCircle.className = 'w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs bg-slate-800 text-slate-400 transition-colors duration-300';
				numCircle.innerHTML = stepNum;
				if (label) label.className = 'mt-2 text-2xs font-semibold text-slate-500 tracking-wider hidden sm:inline';
			}
		});

		const lines = container.querySelectorAll('.step-line');
		lines.forEach(line => {
			const lineNum = parseInt(line.getAttribute('data-step-line'), 10);
			if (lineNum < currentStep) {
				line.className = 'h-0.5 bg-emerald-500 flex-1 mx-2 -mt-4 step-line transition-colors duration-300';
			} else {
				line.className = 'h-0.5 bg-slate-800 flex-1 mx-2 -mt-4 step-line transition-colors duration-300';
			}
		});
	}

	/**
	 * Toggle Step Content Pages
	 */
	function showStep(step) {
		const steps = container.querySelectorAll('.form-step');
		steps.forEach(stepEl => {
			const stepNum = parseInt(stepEl.getAttribute('data-step-content'), 10);
			if (stepNum === step) {
				stepEl.classList.remove('hidden');
			} else {
				stepEl.classList.add('hidden');
			}
		});

		// Manage Navigation Buttons
		if (step === 1) {
			prevBtn.classList.add('hidden');
			nextBtn.classList.remove('hidden');
			submitBtn.classList.add('hidden');
		} else if (step === totalSteps) {
			prevBtn.classList.remove('hidden');
			nextBtn.classList.add('hidden');
			submitBtn.classList.remove('hidden');
		} else {
			prevBtn.classList.remove('hidden');
			nextBtn.classList.remove('hidden');
			submitBtn.classList.add('hidden');
		}

		hideError();
		updateStepIndicators();
		manageNativeButtonVisibility();
	}

	/**
	 * Error Alert Control
	 */
	function showError(msg) {
		errorText.textContent = msg;
		errorAlert.classList.remove('hidden');
		errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
	}

	function hideError() {
		errorText.textContent = '';
		errorAlert.classList.add('hidden');
	}

	/**
	 * Form Validation Rules per Step
	 */
	function validateStep(step) {
		if (step === 1) {
			const service = container.querySelector('input[name="ksms_service_type"]:checked');
			if (!service) {
				showError('Please select a tailoring service.');
				return false;
			}
		}

		if (step === 2) {
			const garment = container.querySelector('input[name="ksms_garment_type"]:checked');
			if (!garment) {
				showError('Please select a garment type.');
				return false;
			}
		}

		if (step === 3) {
			const serviceType = container.querySelector('input[name="ksms_service_type"]:checked').value;
			if (serviceType === 'stitched') {
				const standardSize = container.querySelector('input[name="ksms_standard_size"]:checked');
				if (!standardSize) {
					showError('Please select a standard size from the options.');
					return false;
				}
			} else if (serviceType === 'custom') {
				const measurementInputs = container.querySelectorAll('#sizing-custom-container input[type="number"]');
				for (let input of measurementInputs) {
					const val = parseFloat(input.value);
					const labelText = container.querySelector(`label[for="${input.id}"]`).textContent.trim();
					if (isNaN(val) || val <= 0) {
						input.focus();
						showError(`Please enter a valid positive numeric value for: ${labelText} in inches.`);
						return false;
					}
					const minLimit = parseFloat(input.getAttribute('min'));
					const maxLimit = parseFloat(input.getAttribute('max'));
					if (val < minLimit || val > maxLimit) {
						input.focus();
						showError(`${labelText} must be a valid size between ${minLimit}" and ${maxLimit}".`);
						return false;
					}
				}
			}
		}

		if (step === 5) {
			const name = document.getElementById('ksms_inp_name').value.trim();
			const email = document.getElementById('ksms_inp_email').value.trim();
			const phone = document.getElementById('ksms_inp_phone').value.trim();

			if (!name) {
				showError('Please enter your full name.');
				return false;
			}

			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			if (!email || !emailRegex.test(email)) {
				showError('Please enter a valid email address.');
				return false;
			}

			const phoneRegex = /^\+?[0-9\s\-()]{7,20}$/;
			if (!phone || !phoneRegex.test(phone)) {
				showError('Please enter a valid WhatsApp or phone number (e.g. +923001234567).');
				return false;
			}
		}

		return true;
	}

	// Next Button Click Navigation
	nextBtn.addEventListener('click', function() {
		if (validateStep(currentStep)) {
			if (currentStep < totalSteps) {
				currentStep++;
				showStep(currentStep);
			}
		}
	});

	// Back Button Click Navigation
	prevBtn.addEventListener('click', function() {
		if (currentStep > 1) {
			currentStep--;
			showStep(currentStep);
		}
	});

	/**
	 * SVG Mannequin Active Part Highlight Handling
	 */
	if (svgElement) {
		const measurementFields = container.querySelectorAll('#sizing-custom-container input[type="number"]');
		
		measurementFields.forEach(input => {
			// Extract part identifier name from field name (e.g. ksms_m_chest -> chest)
			const partName = input.getAttribute('name').replace('ksms_m_', '');
			const labelText = container.querySelector(`label[for="${input.id}"]`).textContent.trim();

			// Listen for input focus
			input.addEventListener('focus', function() {
				svgElement.setAttribute('data-active-part', partName);
				svgLabel.textContent = `Measuring: ${labelText}`;
				svgLabel.className = 'bg-amber-500/20 border border-amber-500/50 text-amber-300 font-bold px-4 py-1.5 rounded-full text-xs uppercase tracking-widest transition';
			});

			// Listen for input blur
			input.addEventListener('blur', function() {
				if (svgElement.getAttribute('data-active-part') === partName) {
					svgElement.setAttribute('data-active-part', '');
					svgLabel.textContent = 'Interactive Guide';
					svgLabel.className = 'bg-amber-500/10 border border-amber-500/30 text-amber-400 font-semibold px-4 py-1.5 rounded-full text-xs uppercase tracking-widest transition';
				}
			});
		});

		// Setup reverse SVG path click -> Focus field mapping
		const svgPaths = svgElement.querySelectorAll('[data-body-part]');
		svgPaths.forEach(path => {
			path.addEventListener('click', function() {
				const partName = path.getAttribute('data-body-part');
				const matchingInput = container.querySelector(`input[name="ksms_m_${partName}"]`);
				if (matchingInput) {
					matchingInput.focus();
				}
			});
		});
	}

	/**
	 * Intercept WooCommerce form submissions to validate steps
	 */
	if (wcCartForm) {
		wcCartForm.addEventListener('submit', function(e) {
			// Check if we have completed all measurement steps
			if (currentStep < totalSteps || !validateStep(totalSteps)) {
				e.preventDefault();
				showError('Please complete all tailoring measurement steps before adding this product to the cart.');
				container.scrollIntoView({ behavior: 'smooth', block: 'start' });
				return false;
			}

			// Show visual loading spinner in our overlay during native submission
			statusOverlay.classList.remove('hidden');
		});
	}

	/**
	 * Custom Sizing Submit Button - Triggers WooCommerce Add to Cart
	 */
	submitBtn.addEventListener('click', function() {
		if (validateStep(totalSteps)) {
			// Trigger WooCommerce native submission
			if (nativeSubmitBtn) {
				nativeSubmitBtn.click();
			} else if (wcCartForm) {
				wcCartForm.submit();
			} else {
				showError('WooCommerce Add to Cart form was not found on this page.');
			}
		}
	});

	/**
	 * Custom Radio Card UI selection styling triggers
	 */
	function handleCardHighlights() {
		const cards = container.querySelectorAll('.select-card');
		cards.forEach(card => {
			const radio = card.querySelector('input[type="radio"]');
			if (!radio) return;

			// Initialize current state styling
			if (radio.checked) {
				card.classList.add('border-amber-500', 'bg-amber-500/5');
				card.classList.remove('border-slate-800', 'bg-slate-950/60');
			} else {
				card.classList.remove('border-amber-500', 'bg-amber-500/5');
				card.classList.add('border-slate-800', 'bg-slate-950/60');
			}

			// Add click listener
			card.addEventListener('click', function() {
				if (radio.type === 'radio') {
					// Clear sibling selection classes
					const groupName = radio.name;
					const groupRadios = container.querySelectorAll(`input[name="${groupName}"]`);
					groupRadios.forEach(gRadio => {
						const gCard = gRadio.closest('.select-card');
						if (gCard) {
							gCard.classList.remove('border-amber-500', 'bg-amber-500/5');
							gCard.classList.add('border-slate-800', 'bg-slate-950/60');
						}
					});
				}
				radio.checked = true;
				card.classList.add('border-amber-500', 'bg-amber-500/5');
				card.classList.remove('border-slate-800', 'bg-slate-950/60');
				
				if (radio.name === 'ksms_service_type') {
					updateConditionalStep3Views();
				}
			});
		});
	}

	handleCardHighlights();
});
