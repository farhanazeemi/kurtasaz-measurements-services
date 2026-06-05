<?php
/**
 * Frontend Multi-Step Form Template (WooCommerce Integrated)
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!-- Premium Sizing Form Wrapper -->
<div id="ksms-measurements-form-container" class="w-full max-w-4xl mx-auto my-6 bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl overflow-hidden font-sans text-slate-100">
	<!-- Header Banner -->
	<div class="px-6 py-8 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-center border-b border-slate-800">
		<h2 class="text-2xl md:text-3xl font-extrabold tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-amber-200">
			KURTASAZ
		</h2>
		<p class="mt-1.5 text-xs uppercase tracking-widest text-slate-400 font-semibold">
			Bespoke Tailoring & Measurements
		</p>
	</div>

	<!-- Step Progress Indicator -->
	<div class="px-6 py-5 bg-slate-950/40 border-b border-slate-800">
		<div class="flex items-center justify-between max-w-xl mx-auto">
			<!-- Step 1 -->
			<div class="flex flex-col items-center flex-1 step-indicator" data-step="1">
				<div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs bg-amber-500 text-slate-950 transition-colors duration-300">1</div>
				<span class="mt-2 text-2xs font-semibold text-amber-400 tracking-wider hidden sm:inline"><?php esc_html_e( 'Service', 'kurtasaz-measurements-services' ); ?></span>
			</div>
			<div class="h-0.5 bg-slate-800 flex-1 mx-2 -mt-4 step-line" data-step-line="1"></div>
			<!-- Step 2 -->
			<div class="flex flex-col items-center flex-1 step-indicator" data-step="2">
				<div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs bg-slate-800 text-slate-400 transition-colors duration-300">2</div>
				<span class="mt-2 text-2xs font-semibold text-slate-500 tracking-wider hidden sm:inline"><?php esc_html_e( 'Garment', 'kurtasaz-measurements-services' ); ?></span>
			</div>
			<div class="h-0.5 bg-slate-800 flex-1 mx-2 -mt-4 step-line" data-step-line="2"></div>
			<!-- Step 3 -->
			<div class="flex flex-col items-center flex-1 step-indicator" data-step="3">
				<div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs bg-slate-800 text-slate-400 transition-colors duration-300">3</div>
				<span class="mt-2 text-2xs font-semibold text-slate-500 tracking-wider hidden sm:inline"><?php esc_html_e( 'Sizing', 'kurtasaz-measurements-services' ); ?></span>
			</div>
			<div class="h-0.5 bg-slate-800 flex-1 mx-2 -mt-4 step-line" data-step-line="3"></div>
			<!-- Step 4 -->
			<div class="flex flex-col items-center flex-1 step-indicator" data-step="4">
				<div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs bg-slate-800 text-slate-400 transition-colors duration-300">4</div>
				<span class="mt-2 text-2xs font-semibold text-slate-500 tracking-wider hidden sm:inline"><?php esc_html_e( 'Notes', 'kurtasaz-measurements-services' ); ?></span>
			</div>
			<div class="h-0.5 bg-slate-800 flex-1 mx-2 -mt-4 step-line" data-step-line="4"></div>
			<!-- Step 5 -->
			<div class="flex flex-col items-center flex-1 step-indicator" data-step="5">
				<div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs bg-slate-800 text-slate-400 transition-colors duration-300">5</div>
				<span class="mt-2 text-2xs font-semibold text-slate-500 tracking-wider hidden sm:inline"><?php esc_html_e( 'Details', 'kurtasaz-measurements-services' ); ?></span>
			</div>
		</div>
	</div>

	<!-- Form Content Container (DIV to prevent nested form issues in WooCommerce) -->
	<div id="ksms-tailoring-form" class="p-6 md:p-10 space-y-6">
		<!-- Error Alert Box -->
		<div id="ksms-form-error" class="hidden p-4 bg-red-950/40 border border-red-800 text-red-300 text-sm rounded-xl flex items-center space-x-2">
			<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
			</svg>
			<span id="ksms-error-text"></span>
		</div>

		<!-- STEP 1: Service Selection -->
		<div class="form-step" data-step-content="1">
			<h3 class="text-lg font-bold mb-4 text-slate-200"><?php esc_html_e( 'Select Your Tailoring Service', 'kurtasaz-measurements-services' ); ?></h3>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
				<!-- Service Unstitched -->
				<label class="relative flex flex-col p-5 bg-slate-950/60 border-2 border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 transition-all duration-300 select-card">
					<input type="radio" name="ksms_service_type" value="unstitched" class="absolute top-4 right-4 text-amber-500 focus:ring-amber-500 bg-slate-900 border-slate-700 w-4 h-4" checked>
					<span class="text-2xl mb-3">🪡</span>
					<span class="font-bold text-slate-100 text-base"><?php esc_html_e( 'Unstitched Fabric', 'kurtasaz-measurements-services' ); ?></span>
					<span class="mt-1.5 text-xs text-slate-400"><?php esc_html_e( 'Order premium fabric only. Sizing recommendations will be provided.', 'kurtasaz-measurements-services' ); ?></span>
				</label>
				<!-- Service Stitched -->
				<label class="relative flex flex-col p-5 bg-slate-950/60 border-2 border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 transition-all duration-300 select-card">
					<input type="radio" name="ksms_service_type" value="stitched" class="absolute top-4 right-4 text-amber-500 focus:ring-amber-500 bg-slate-900 border-slate-700 w-4 h-4">
					<span class="text-2xl mb-3">👔</span>
					<span class="font-bold text-slate-100 text-base"><?php esc_html_e( 'Standard Stitched', 'kurtasaz-measurements-services' ); ?></span>
					<span class="mt-1.5 text-xs text-slate-400"><?php esc_html_e( 'Choose standard sizes (S, M, L, XL, XXL) stitched to perfection.', 'kurtasaz-measurements-services' ); ?></span>
				</label>
				<!-- Service Custom -->
				<label class="relative flex flex-col p-5 bg-slate-950/60 border-2 border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 transition-all duration-300 select-card">
					<input type="radio" name="ksms_service_type" value="custom" class="absolute top-4 right-4 text-amber-500 focus:ring-amber-500 bg-slate-900 border-slate-700 w-4 h-4">
					<span class="text-2xl mb-3">📏</span>
					<span class="font-bold text-slate-100 text-base"><?php esc_html_e( 'Custom Silwaai', 'kurtasaz-measurements-services' ); ?></span>
					<span class="mt-1.5 text-xs text-slate-400"><?php esc_html_e( 'Provide exact measurements using our interactive guide for a custom fit.', 'kurtasaz-measurements-services' ); ?></span>
				</label>
			</div>
		</div>

		<!-- STEP 2: Garment Type -->
		<div class="form-step hidden" data-step-content="2">
			<h3 class="text-lg font-bold mb-4 text-slate-200"><?php esc_html_e( 'Choose Your Garment Type', 'kurtasaz-measurements-services' ); ?></h3>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
				<!-- Garment Kurta -->
				<label class="relative flex flex-col p-5 bg-slate-950/60 border-2 border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 transition-all duration-300 select-card">
					<input type="radio" name="ksms_garment_type" value="kurta" class="absolute top-4 right-4 text-amber-500 focus:ring-amber-500 bg-slate-900 border-slate-700 w-4 h-4" checked>
					<span class="text-2xl mb-3">🧥</span>
					<span class="font-bold text-slate-100 text-base"><?php esc_html_e( 'Kurta Only', 'kurtasaz-measurements-services' ); ?></span>
					<span class="mt-1.5 text-xs text-slate-400"><?php esc_html_e( 'A custom or standard fit Kurta top only.', 'kurtasaz-measurements-services' ); ?></span>
				</label>
				<!-- Garment Shalwar -->
				<label class="relative flex flex-col p-5 bg-slate-950/60 border-2 border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 transition-all duration-300 select-card">
					<input type="radio" name="ksms_garment_type" value="shalwar" class="absolute top-4 right-4 text-amber-500 focus:ring-amber-500 bg-slate-900 border-slate-700 w-4 h-4">
					<span class="text-2xl mb-3">👖</span>
					<span class="font-bold text-slate-100 text-base"><?php esc_html_e( 'Shalwar Only', 'kurtasaz-measurements-services' ); ?></span>
					<span class="mt-1.5 text-xs text-slate-400"><?php esc_html_e( 'Custom sized matching bottom shalwar only.', 'kurtasaz-measurements-services' ); ?></span>
				</label>
				<!-- Garment Full Suit -->
				<label class="relative flex flex-col p-5 bg-slate-950/60 border-2 border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 transition-all duration-300 select-card">
					<input type="radio" name="ksms_garment_type" value="full_suit" class="absolute top-4 right-4 text-amber-500 focus:ring-amber-500 bg-slate-900 border-slate-700 w-4 h-4">
					<span class="text-2xl mb-3">🥋</span>
					<span class="font-bold text-slate-100 text-base"><?php esc_html_e( 'Full Suit', 'kurtasaz-measurements-services' ); ?></span>
					<span class="mt-1.5 text-xs text-slate-400"><?php esc_html_e( 'Complete ensemble matching both Kurta & Shalwar.', 'kurtasaz-measurements-services' ); ?></span>
				</label>
			</div>
		</div>

		<!-- STEP 3: Sizing Specifications (Conditional) -->
		<div class="form-step hidden" data-step-content="3">
			<!-- A. Unstitched Guidelines -->
			<div id="sizing-unstitched-container" class="hidden space-y-4">
				<h3 class="text-lg font-bold text-slate-200"><?php esc_html_e( 'Unstitched Fabric Size Guidelines', 'kurtasaz-measurements-services' ); ?></h3>
				<div class="p-5 bg-amber-500/10 border border-amber-500/30 rounded-xl space-y-3">
					<div class="flex items-start space-x-3">
						<span class="text-lg mt-0.5 text-amber-400">💡</span>
						<div>
							<h4 class="font-bold text-amber-300 text-sm"><?php esc_html_e( 'Standard Fabric Yards Dispatched', 'kurtasaz-measurements-services' ); ?></h4>
							<p class="mt-1 text-slate-300 text-xs leading-relaxed">
								<?php esc_html_e( 'For unstitched fabric, we supply the following standard yards:', 'kurtasaz-measurements-services' ); ?>
							</p>
							<ul class="list-disc list-inside mt-2 text-2xs text-slate-400 space-y-1">
								<li><?php esc_html_e( 'Full Suit: 4.5 Meters (Standard width 54")', 'kurtasaz-measurements-services' ); ?></li>
								<li><?php esc_html_e( 'Kurta Only: 2.5 Meters (Standard width 54")', 'kurtasaz-measurements-services' ); ?></li>
								<li><?php esc_html_e( 'Shalwar Only: 2.25 Meters (Standard width 54")', 'kurtasaz-measurements-services' ); ?></li>
							</ul>
							<p class="mt-3 text-slate-400 text-2xs italic">
								<?php esc_html_e( '* Please consult your tailor before placing the order to ensure these standard dimensions fit your requirements.', 'kurtasaz-measurements-services' ); ?>
							</p>
						</div>
					</div>
				</div>
			</div>

			<!-- B. Stitched Sizes -->
			<div id="sizing-stitched-container" class="hidden space-y-4">
				<h3 class="text-lg font-bold text-slate-200"><?php esc_html_e( 'Select Your Standard Size', 'kurtasaz-measurements-services' ); ?></h3>
				<div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
					<!-- Size S -->
					<label class="flex flex-col items-center justify-center p-4 bg-slate-950/60 border border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 select-card text-center transition-all">
						<input type="radio" name="ksms_standard_size" value="S" class="hidden">
						<span class="text-lg font-black text-slate-100">S</span>
						<span class="mt-0.5 text-2xs text-slate-500 uppercase"><?php esc_html_e( 'Small', 'kurtasaz-measurements-services' ); ?></span>
						<span class="mt-1.5 text-2xs font-semibold text-slate-400"><?php esc_html_e( 'Chest: 38"', 'kurtasaz-measurements-services' ); ?></span>
					</label>
					<!-- Size M -->
					<label class="flex flex-col items-center justify-center p-4 bg-slate-950/60 border border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 select-card text-center transition-all">
						<input type="radio" name="ksms_standard_size" value="M" class="hidden">
						<span class="text-lg font-black text-slate-100">M</span>
						<span class="mt-0.5 text-2xs text-slate-500 uppercase"><?php esc_html_e( 'Medium', 'kurtasaz-measurements-services' ); ?></span>
						<span class="mt-1.5 text-2xs font-semibold text-slate-400"><?php esc_html_e( 'Chest: 40"', 'kurtasaz-measurements-services' ); ?></span>
					</label>
					<!-- Size L -->
					<label class="flex flex-col items-center justify-center p-4 bg-slate-950/60 border border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 select-card text-center transition-all">
						<input type="radio" name="ksms_standard_size" value="L" class="hidden">
						<span class="text-lg font-black text-slate-100">L</span>
						<span class="mt-0.5 text-2xs text-slate-500 uppercase"><?php esc_html_e( 'Large', 'kurtasaz-measurements-services' ); ?></span>
						<span class="mt-1.5 text-2xs font-semibold text-slate-400"><?php esc_html_e( 'Chest: 42"', 'kurtasaz-measurements-services' ); ?></span>
					</label>
					<!-- Size XL -->
					<label class="flex flex-col items-center justify-center p-4 bg-slate-950/60 border border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 select-card text-center transition-all">
						<input type="radio" name="ksms_standard_size" value="XL" class="hidden">
						<span class="text-lg font-black text-slate-100">XL</span>
						<span class="mt-0.5 text-2xs text-slate-500 uppercase"><?php esc_html_e( 'Extra Large', 'kurtasaz-measurements-services' ); ?></span>
						<span class="mt-1.5 text-2xs font-semibold text-slate-400"><?php esc_html_e( 'Chest: 44"', 'kurtasaz-measurements-services' ); ?></span>
					</label>
					<!-- Size XXL -->
					<label class="flex flex-col items-center justify-center p-4 bg-slate-950/60 border border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 select-card text-center transition-all">
						<input type="radio" name="ksms_standard_size" value="XXL" class="hidden">
						<span class="text-lg font-black text-slate-100">XXL</span>
						<span class="mt-0.5 text-2xs text-slate-500 uppercase"><?php esc_html_e( '2X Large', 'kurtasaz-measurements-services' ); ?></span>
						<span class="mt-1.5 text-2xs font-semibold text-slate-400"><?php esc_html_e( 'Chest: 46"', 'kurtasaz-measurements-services' ); ?></span>
					</label>
				</div>
			</div>

			<!-- C. Custom Sizing Grid -->
			<div id="sizing-custom-container" class="hidden space-y-4">
				<h3 class="text-lg font-bold text-slate-200"><?php esc_html_e( 'Enter Your Dimensions (Inches)', 'kurtasaz-measurements-services' ); ?></h3>
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
					<!-- Inputs -->
					<div class="grid grid-cols-2 gap-3">
						<!-- Neck -->
						<div class="flex flex-col">
							<label for="ksms_inp_neck" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Neck', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="5" max="30" id="ksms_inp_neck" name="ksms_m_neck" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 14.5">
						</div>
						<!-- Shoulder -->
						<div class="flex flex-col">
							<label for="ksms_inp_shoulder" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Shoulder', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="10" max="40" id="ksms_inp_shoulder" name="ksms_m_shoulder" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 18.0">
						</div>
						<!-- Chest -->
						<div class="flex flex-col">
							<label for="ksms_inp_chest" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Chest', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="20" max="80" id="ksms_inp_chest" name="ksms_m_chest" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 40.0">
						</div>
						<!-- Waist -->
						<div class="flex flex-col">
							<label for="ksms_inp_waist" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Waist', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="20" max="80" id="ksms_inp_waist" name="ksms_m_waist" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 36.5">
						</div>
						<!-- Hip -->
						<div class="flex flex-col">
							<label for="ksms_inp_hip" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Hip', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="20" max="85" id="ksms_inp_hip" name="ksms_m_hip" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 42.0">
						</div>
						<!-- Kurta Length -->
						<div class="flex flex-col">
							<label for="ksms_inp_kurta_length" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Kurta Length', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="20" max="70" id="ksms_inp_kurta_length" name="ksms_m_kurta_length" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 40.0">
						</div>
						<!-- Sleeve Length -->
						<div class="flex flex-col">
							<label for="ksms_inp_sleeve_length" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Sleeve Length', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="10" max="45" id="ksms_inp_sleeve_length" name="ksms_m_sleeve_length" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 24.5">
						</div>
						<!-- Armhole -->
						<div class="flex flex-col">
							<label for="ksms_inp_armhole" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Armhole', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="5" max="35" id="ksms_inp_armhole" name="ksms_m_armhole" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 19.0">
						</div>
						<!-- Bicep -->
						<div class="flex flex-col">
							<label for="ksms_inp_bicep" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Bicep', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="5" max="30" id="ksms_inp_bicep" name="ksms_m_bicep" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 15.0">
						</div>
						<!-- Wrist -->
						<div class="flex flex-col">
							<label for="ksms_inp_wrist" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Wrist', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="4" max="20" id="ksms_inp_wrist" name="ksms_m_wrist" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 9.5">
						</div>
						<!-- Collar -->
						<div class="flex flex-col">
							<label for="ksms_inp_collar" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Collar Size', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="5" max="25" id="ksms_inp_collar" name="ksms_m_collar" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 16.0">
						</div>
						<!-- Inseam -->
						<div class="flex flex-col">
							<label for="ksms_inp_inseam_length" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Inseam / Shalwar Length', 'kurtasaz-measurements-services' ); ?></label>
							<input type="number" step="0.1" min="20" max="65" id="ksms_inp_inseam_length" name="ksms_m_inseam_length" class="bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. 38.0">
						</div>
					</div>

					<!-- SVG Dummy Diagram -->
					<div class="flex flex-col items-center justify-center bg-slate-950/40 border border-slate-800 rounded-xl p-5 min-h-[320px] relative">
						<div class="text-center mb-3">
							<span id="svg-active-part-label" class="bg-amber-500/10 border border-amber-500/30 text-amber-400 font-semibold px-3 py-1 rounded-full text-2xs uppercase tracking-widest">
								<?php esc_html_e( 'Interactive Guide', 'kurtasaz-measurements-services' ); ?>
							</span>
						</div>

						<svg id="ksms-svg-body" class="w-full max-w-[240px] h-[280px]" viewBox="0 0 100 135" xmlns="http://www.w3.org/2000/svg" data-active-part="">
							<!-- Silhouette -->
							<path d="M50 8 C53 8, 55 10, 55 13 C55 17, 45 17, 45 13 C45 10, 47 8, 50 8 Z" fill="#334155" opacity="0.3"></path>
							<path d="M44 16 C35 18, 32 23, 26 31 C22 36, 17 48, 14 58 C13 62, 14 65, 16 65 C17 65, 19 63, 20 60 C23 51, 26 42, 28 37 C28 45, 29 65, 30 75 C31 85, 31 100, 31 125 C31 128, 33 129, 34 129 C35 129, 36 128, 36 125 C37 105, 38 88, 41 80 C44 75, 47 75, 50 75 C53 75, 56 75, 59 80 C62 88, 63 105, 64 125 C64 128, 65 129, 66 129 C67 129, 69 128, 69 125 C69 100, 69 85, 70 75 C71 65, 72 45, 72 37 C74 42, 77 51, 80 60 C81 63, 83 65, 84 65 C86 65, 87 62, 86 58 C83 48, 78 36, 74 31 C68 23, 65 18, 56 16 Z" fill="#334155" opacity="0.3"></path>
							
							<!-- Lines -->
							<ellipse cx="50" cy="16" rx="5" ry="2" fill="none" stroke="#64748b" data-body-part="neck"></ellipse>
							<path d="M44 17 C48 20, 52 20, 56 17" fill="none" stroke="#64748b" data-body-part="collar"></path>
							<line x1="33" y1="20" x2="67" y2="20" stroke="#64748b" data-body-part="shoulder"></line>
							<line x1="28" y1="35" x2="72" y2="35" stroke="#64748b" data-body-part="chest"></line>
							<line x1="29" y1="52" x2="71" y2="52" stroke="#64748b" data-body-part="waist"></line>
							<line x1="29" y1="70" x2="71" y2="70" stroke="#64748b" data-body-part="hip"></line>
							<line x1="43" y1="20" x2="43" y2="78" stroke="#64748b" stroke-dasharray="2,2" data-body-part="kurta_length"></line>
							<ellipse cx="31" cy="27" rx="3.5" ry="6" fill="none" stroke="#64748b" transform="rotate(-15, 31, 27)" data-body-part="armhole"></ellipse>
							<line x1="24" y1="40" x2="29" y2="42" stroke="#64748b" data-body-part="bicep"></line>
							<path d="M32 21 L16 57" fill="none" stroke="#64748b" stroke-dasharray="2,2" data-body-part="sleeve_length"></path>
							<line x1="14" y1="57" x2="19" y2="59" stroke="#64748b" data-body-part="wrist"></line>
							<line x1="61" y1="75" x2="61" y2="125" stroke="#64748b" stroke-dasharray="2,2" data-body-part="inseam_length"></line>
						</svg>
					</div>
				</div>
			</div>
		</div>

		<!-- STEP 4: Customization Details -->
		<div class="form-step hidden" data-step-content="4">
			<h3 class="text-lg font-bold mb-4 text-slate-200"><?php esc_html_e( 'Fabric Preferences & Special Instructions', 'kurtasaz-measurements-services' ); ?></h3>
			<div class="flex flex-col space-y-3">
				<label for="ksms_inp_notes" class="text-xs font-semibold text-slate-400"><?php esc_html_e( 'Write styling notes, collar types (e.g. Sherwani, Shirt Collar), cuff options, or special fitting instructions:', 'kurtasaz-measurements-services' ); ?></label>
				<textarea id="ksms_inp_notes" name="ksms_fabric_notes" rows="5" class="bg-slate-950 border border-slate-800 rounded-xl p-4 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs leading-relaxed transition" placeholder="<?php esc_html_e( 'Describe your preferences here...', 'kurtasaz-measurements-services' ); ?>"></textarea>
			</div>
		</div>

		<!-- STEP 5: Contact Info -->
		<div class="form-step hidden" data-step-content="5">
			<h3 class="text-lg font-bold mb-4 text-slate-200"><?php esc_html_e( 'Provide Your Contact Information', 'kurtasaz-measurements-services' ); ?></h3>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
				<!-- Name -->
				<div class="flex flex-col">
					<label for="ksms_inp_name" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Full Name', 'kurtasaz-measurements-services' ); ?></label>
					<input type="text" id="ksms_inp_name" name="ksms_client_name" class="bg-slate-950 border border-slate-800 rounded-lg p-3 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. Farhan Ali">
				</div>
				<!-- Email -->
				<div class="flex flex-col">
					<label for="ksms_inp_email" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'Email Address', 'kurtasaz-measurements-services' ); ?></label>
					<input type="email" id="ksms_inp_email" name="ksms_client_email" class="bg-slate-950 border border-slate-800 rounded-lg p-3 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. client@example.com">
				</div>
				<!-- WhatsApp Number -->
				<div class="flex flex-col">
					<label for="ksms_inp_phone" class="text-2xs font-semibold text-slate-400 mb-1"><?php esc_html_e( 'WhatsApp / Phone Number', 'kurtasaz-measurements-services' ); ?></label>
					<input type="tel" id="ksms_inp_phone" name="ksms_client_phone" class="bg-slate-950 border border-slate-800 rounded-lg p-3 text-slate-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-xs transition" placeholder="e.g. +923001234567">
				</div>
			</div>
		</div>

		<!-- Footer Navigation Actions -->
		<div class="flex items-center justify-between pt-4 border-t border-slate-800">
			<!-- Back Button -->
			<button type="button" id="ksms-prev-btn" class="hidden px-5 py-2.5 bg-slate-850 hover:bg-slate-800 text-slate-300 font-semibold rounded-lg text-xs transition-all focus:outline-none border border-slate-700/60">
				&larr; <?php esc_html_e( 'Back', 'kurtasaz-measurements-services' ); ?>
			</button>
			<div class="flex-grow"></div>
			<!-- Next Button -->
			<button type="button" id="ksms-next-btn" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold rounded-lg text-xs transition-all focus:outline-none shadow-lg shadow-amber-500/15">
				<?php esc_html_e( 'Next', 'kurtasaz-measurements-services' ); ?> &rarr;
			</button>
			<!-- Add to Cart (Placeholder - JS will manage triggers) -->
			<button type="button" id="ksms-submit-btn" class="hidden px-6 py-2.5 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 hover:from-amber-300 hover:via-amber-400 hover:to-amber-500 text-slate-950 font-black rounded-lg text-xs tracking-wider uppercase transition-all focus:outline-none shadow-xl shadow-amber-500/25">
				<?php esc_html_e( 'Add Tailor Specs', 'kurtasaz-measurements-services' ); ?>
			</button>
		</div>
	</div>

	<!-- Status/Loading Overlay -->
	<div id="ksms-status-overlay" class="hidden absolute inset-0 bg-slate-950/90 backdrop-blur-sm flex flex-col items-center justify-center p-6 text-center transition-all z-50">
		<div id="ksms-spinner">
			<svg class="animate-spin h-12 w-12 text-amber-500 mx-auto" fill="none" viewBox="0 0 24 24" xmlns="http://www-w3-org.sandbox.google.com/">
				<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
				<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
			</svg>
			<h4 class="text-lg font-bold text-slate-100 mt-4 tracking-wide"><?php esc_html_e( 'Loading Sizing Sheet...', 'kurtasaz-measurements-services' ); ?></h4>
		</div>
	</div>
</div>
