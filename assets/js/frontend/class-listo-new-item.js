listo.class('ListoNewItem', function (listo, $) {
	const { ajax, hooks, data: config } = listo;

	const ANIM_SPEED = +config('animation_speed');

	return class {
		constructor(opts = {}) {
			this.saving = false;

			this.$toggle = $(opts.toggle);
			this.$form = $(opts.form);
			this.$title = this.$form.find('[name=title]');
			this.$cancel = this.$form.find('[data-listo=cancel]');
			this.$save = this.$form.find('[data-listo=save]');

			this.$toggle.on('click', e => e.stopPropagation() || this.show());
			this.$form.on('click', e => e.stopPropagation());
			this.$title.on('keydown', e => this.onKeyDown(e));
			this.$title.on('input', () => this.onInput());
			this.$cancel.on('click', e => e.stopPropagation() || this.hide());
			this.$save.on('click', () => this.submit());

			hooks.addAction('new_collection_form', () => this.hide());
			hooks.addAction('set_current_collection', () => this.hide());
		}

		show() {
			if (this.$form.is(':hidden')) {
				this.$toggle.hide();
				this.$form.fadeIn(ANIM_SPEED);
				listo.focus(this.$title);

				hooks.doAction('new_item_form');
			}
		}

		hide() {
			if (this.$form.is(':visible')) {
				this.$form.hide();
				this.$toggle.fadeIn(ANIM_SPEED);
			}
		}

		value(title) {
			if ('string' === typeof title) {
				this.$title.val(title.trim());
			} else {
				return this.$title.val().trim();
			}
		}

		submit() {
			let title = this.value();
			if (!title) {
				return;
			}

			if (this.saving) return;
			this.saving = true;
			this.onInput();

			// Create item with an empty title.
			ajax('listowp/v1/items', {}, 'POST').done(json => {
				if (json instanceof Array && json[0]) {
					let collection = hooks.applyFilters('current_collection_id');
					let item = Object.assign(json[0], { title, collection });
					let { id } = item;

					let loadTodo = !+collection && ['all', 'inbox'].indexOf(collection) === -1;

					collection = +collection || undefined;

					// Update item title.
					ajax('listowp/v1/items', { id, title, collection }, 'PATCH').always(() => {
						this.value('');
						this.saving = false;
						this.onInput();
						listo.focus(this.$title);

						if (loadTodo) {
							hooks.doAction('switch_collection', 'all');
						} else {
							hooks.doAction('item_added', item);
						}
					});
				}
			});
		}

		onKeyDown(e) {
			if ('Enter' === e.code) {
				e.preventDefault();
				e.stopPropagation();
				this.$save.trigger('click');
			} else if ('Escape' === e.code) {
				this.hide();
			}
		}

		onInput() {
			this.value() && !this.saving
				? this.$save.removeAttr('disabled')
				: this.$save.attr('disabled', 'disabled');
		}
	};
});
