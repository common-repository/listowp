(function (global, $, _, listoAdminData = {}) {
	const REST_URL = global.wpApiSettings.root;
	const REST_NONCE = global.wpApiSettings.nonce;

	const templates = listoAdminData.templates;

	class ListoAdmin {
		constructor(container, position) {
			this.$el = $(container);
			this.$el.on('click', 'input[type=checkbox]', e => this.onCheckboxClick(e));
			this.$el.on('click', '[data-listo=checkbox]', e => this.onCheckboxToggleClick(e));
			this.$el.on('input', 'input[type=text], input[type=number]', e => this.onTextInput(e));
			this.$el.on('click', 'button[data-btn=cancel]', e => this.onCancelClick(e));
			this.$el.on('click', 'button[data-btn=save]', e => this.onSaveClick(e));
			this.$el.on('change', 'select', e => this.onSelectChange(e));

			ajax('listowp/v1/admin', { position }).done(json => this.render(json));
		}

		render(json) {
			this.$el.empty();

			$.each(json, (groupId, group) => {
				// Skip config group with no items.
				if ('object' !== typeof group.items || !Object.keys(group.items).length) {
					return true;
				}

				let content = '';

				$.each(group.items, (itemId, item) => {
					let itemTmpl = templates[`config_item_${item.type}`];

					if (itemTmpl) {
						item.label = item.title;
						content += template(itemTmpl)(item);
					} else {
						console.log('Unknown admin item ' + item.type);
					}
				});

				let configTmpl = template(templates.config_group);
				let configHtml = configTmpl(Object.assign(group, { content }));

				this.$el.append(configHtml);
			});

			this.toggleChildren({ animate: false });
		}

		toggleChildren(parent, opts = { disable: null, visible: null, animate: true }) {
			if (typeof parent === 'object') {
				opts = parent;
				parent = null;
			}

			if (parent && typeof parent === 'string') {
				let $children = this.$el.find(`[data-parent=${parent}]`);
				let $inputs = $children.find('[type=text], [type=number], [type=checkbox]');
				let $buttons = $children.find('[data-btn=cancel], [data-btn=save]');

				if (opts.disable) {
					$children.css('opacity', 0.5);
					$inputs.add($buttons).attr('disabled', 'disable');
				} else {
					$children.css('opacity', '');
					$inputs.add($buttons).removeAttr('disabled');

					if (opts.visible === true) {
						opts.animate ? $children.slideDown('fast') : $children.show();
					} else if (opts.visible === false) {
						opts.animate ? $children.slideUp('fast') : $children.hide();
					}
				}
			} else {
				let parentIds = this.$el
					.find(`[data-parent]`)
					.map((i, el) => el.getAttribute('data-parent'))
					.toArray()
					.filter(id => !!id);

				let uniqueParentIds = parentIds.filter((item, i, ar) => ar.indexOf(item) === i);

				uniqueParentIds.forEach(id => {
					let $parent = this.$el.find(`[id=${id}]`);
					let checked = $parent.is(':checked');

					this.toggleChildren(id, { visible: checked, animate: opts.animate });
				});
			}
		}

		onCheckboxClick(e) {
			e.stopPropagation();

			let $checkbox = $(e.currentTarget);
			let $config = $checkbox.closest('[data-type]');
			let $toggle = $config.find('[data-listo=checkbox]');

			let name = $checkbox.attr('id');
			let value = $checkbox.is(':checked') ? 1 : 0;

			progress($config, 'loading');
			this.toggleChildren(name, { disable: true });
			ajax('listowp/v1/admin', { name, value }, 'POST')
				.done(() => {
					progress($config, 'done', () => {
						this.toggleChildren(name, { visible: !!value });
					});
				})
				.fail(() => progress($config, 'fail'));

			// Update toggle class.
			$toggle.removeClass(value ? 'fa-toggle-off' : 'fa-toggle-on');
			$toggle.addClass(value ? 'fa-toggle-on' : 'fa-toggle-off');
		}

		onCheckboxToggleClick(e) {
			e.stopPropagation();

			let $toggle = $(e.currentTarget);
			let $checkbox = $toggle.next('input[type=checkbox]');

			$checkbox.trigger('click');
		}

		onTextInput(e) {
			e.stopPropagation();

			let $input = $(e.currentTarget);
			let $config = $input.closest('[data-type]');
			let $cancel = $config.find('button[data-btn=cancel]');
			let $save = $config.find('button[data-btn=save]');
			let value = $input.val().trim();
			let previousValue = $input.attr('data-value');

			if (value === previousValue) {
				$cancel.hide();
				$save.hide();
			} else {
				$cancel.show();
				$save.show();
			}
		}

		onCancelClick(e) {
			e.stopPropagation();

			let $cancel = $(e.currentTarget);
			let $config = $cancel.closest('[data-type]');
			let $input = $config.find('[type=text], [type=number]');
			let $save = $config.find('button[data-btn=save]');

			$input.val($input.attr('data-value'));
			$cancel.hide();
			$save.hide();
		}

		onSaveClick(e) {
			e.stopPropagation();

			let $save = $(e.currentTarget);
			let $config = $save.closest('[data-type]');
			let $input = $config.find('[type=text], [type=number]');
			let $cancel = $config.find('button[data-btn=cancel]');

			let name = $input.attr('id');
			let value = $input.val().trim();

			$cancel.hide();
			$save.hide();

			progress($config, 'loading');
			ajax('listowp/v1/admin', { name, value }, 'POST')
				.done(() => {
					$input.attr('data-value', value);
					progress($config, 'done');
				})
				.fail(() => progress($config, 'fail'));
		}

		onSelectChange(e) {
			e.stopPropagation();

			let $select = $(e.currentTarget);
			let $config = $select.closest('[data-type]');

			let name = $select.attr('id');
			let value = $select.val();

			progress($config, 'loading');
			ajax('listowp/v1/admin', { name, value }, 'POST')
				.done(() => {
					$select.attr('data-value', value);
					progress($config, 'done');
				})
				.fail(() => progress($config, 'fail'));
		}
	}

	function ajax(endpoint, params = {}, method = 'GET') {
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
	}

	function template(templateString) {
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
	}

	function progress(el, type, onComplete) {
		let $config = $(el).closest('[data-type]');
		let $inputs = $config.find('[type=text], [type=number], [type=checkbox]');
		let $buttons = $config.find('[data-btn=cancel], [data-btn=save]');
		let $loading = $config.find('[data-loading]');
		let $check = $config.find('[data-check]');

		if (type === 'loading') {
			$config.data('force-loading', 1).css('opacity', 0.5);
			$inputs.attr('disabled', 'disabled');
			$buttons.attr('disabled', 'disabled');
			$loading.stop().show();
			$check.stop().hide();

			// Force loading progress for current config to be shown for at least 1 second.
			setTimeout(function () {
				$config.removeData('force-loading');
				let queue = $config.data('queue');
				if (queue) {
					$config.removeData('queue');
					progress($config, queue.type, queue.onComplete);
				}
			}, 1000);
		} else if ($config.data('force-loading')) {
			$config.data('queue', { type, onComplete });
		} else if (type === 'done') {
			$config.css('opacity', '');
			$inputs.removeAttr('disabled');
			$buttons.removeAttr('disabled');
			$loading.stop().hide();
			$check.stop().show().delay(1000).fadeOut();
			if ('function' === typeof onComplete) {
				onComplete();
			}
		} else if (type === 'fail') {
			$config.css('opacity', '');
			$inputs.removeAttr('disabled');
			$buttons.removeAttr('disabled');
			$loading.stop().hide();
			$check.stop().hide();
			if ('function' === typeof onComplete) {
				onComplete();
			}
		}
	}

	$(function () {
		['left', 'right'].forEach(position => {
			let container = document.getElementById(`listo_admin_body_${position}`);
			new ListoAdmin(container, position);
		});
	});
})(window, window.jQuery, window._, window.listoAdminData);
