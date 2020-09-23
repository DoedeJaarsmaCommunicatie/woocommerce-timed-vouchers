(function () {
	'use strict';

	const TimedVouchers = {
		data() {
			return {
				typeSelector: document.querySelector('#product-type'),
				generalProductData: document.querySelector('#general_product_data .options_group.pricing.show_if_simple'),
				selectors: {
					generalOptions: document.querySelector('.product_data_tabs li.general_options'),
				},

			}
		},
		init() {
			this.data().generalProductData.classList.add('show_if_timed-voucher');
			this._selectorEvents();
		},

		_selectorEvents() {
			this.data().typeSelector.addEventListener('change', (e) => {
				if (e.target.value !== 'timed-voucher') {
					return;
				}

				this.data().selectors.generalOptions.style.display = '';
			});
		}
	};

	TimedVouchers.init();

}());
