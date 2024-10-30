(function (global, $, _, listoData = {}) {
	const REST_URL = global.wpApiSettings.root;
	const REST_NONCE = global.wpApiSettings.nonce;

	const listo = {
		/**
		 * Request wrapper to call REST API endpoint.
		 *
		 * @param {string} endpoint
		 * @param {Object} params
		 * @param {string} method
		 * @returns {jQuery.ajax}
		 */
		ajax(endpoint, params = {}, method = 'GET') {
			return $.ajax({
				url: `${REST_URL}${endpoint}`,
				method,
				data: params,
				dataType: 'json',
				beforeSend(xhr) {
					xhr.setRequestHeader('X-WP-Nonce', REST_NONCE);
				}
			}).fail(xhr => {
				if (xhr && xhr.responseJSON) {
					alert(xhr.responseJSON.message);
				}
			});
		},

		/**
		 * Template string compiler.
		 *
		 * @param {string} templateString
		 * @returns {_.template}
		 */
		template(templateString) {
			let settings = {
				variable: 'data',
				evaluate: /\{\{([\s\S]+?)\}\}/g,
				interpolate: /\{\{=([\s\S]+?)\}\}/g,
				escape: /\{\{-([\s\S]+?)\}\}/g
			};

			let template = _.template(templateString, settings);

			// Backward-compatibility fix for Underscore prior to version 1.7.0.
			if (typeof template !== 'function') {
				template = _.template(templateString, null, settings);
			}

			return template;
		},

		/**
		 * Get or set listoData value.
		 *
		 * @param {string} key
		 * @param {*} value
		 * @returns {*}
		 */
		data(key = '', value) {
			let data = listoData;
			let result;

			key = key.split('.');
			for (let i = 0; i < key.length; i++) {
				if ('undefined' === typeof data[key[i]]) {
					break;
				}

				if (i === key.length - 1) {
					if ('undefined' === typeof value) {
						result = data[key[i]]; // Get data.
					} else {
						data[key[i]] = value; // Set data.
					}
				} else {
					data = data[key[i]];
				}
			}

			return result;
		},

		/**
		 * Register or retrieve listo classes.
		 *
		 * @param {string} name
		 * @param {Function|undefined} factory
		 * @returns {Object}
		 */
		class(name, factory) {
			if (!name.match(/^Listo([A-Z][a-z]+)*$/)) {
				throw new Error('Invalid Listo class name.');
			}

			if ('function' === typeof factory) {
				const classObject = factory(this, $);
				if ('function' === typeof classObject) {
					this[name] = classObject;
				}
			}

			return this[name];
		},

		/**
		 * Focus and put cursor to the end of an input text.
		 *
		 * @param {Element|jQuery} input
		 */
		focus(input) {
			input = input.jquery ? input[0] : input;
			input.focus();

			if (input.setSelectionRange) {
				let len = input.value.length;
				input.setSelectionRange(len, len);
			}
		}
	};

	global.listo = listo;
})(window, window.jQuery, window._, window.listoData);
