listo.class('ListoNewCollection', function (listo, $) {
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

			hooks.addAction('new_item_form', () => this.hide());

			// Run once on page load.
			hooks.addAction('collections_loaded', 'new_collection_form', () => {
				hooks.removeAction('collections_loaded', 'new_collection_form');
				this.$toggle.show();
			});
		}

		show() {
			if (this.$form.is(':hidden')) {
				this.$toggle.hide();
				this.$form.fadeIn(ANIM_SPEED);
				listo.focus(this.$title);

				hooks.doAction('new_collection_form');
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

			// Create collection with an empty title.
			ajax('listowp/v1/collections', {}, 'POST').done(json => {
				if (json instanceof Array && json[0]) {
					let collection = Object.assign(json[0], { title });
					let { id } = collection;

					// Update collection title.
					ajax('listowp/v1/collections', { id, title }, 'PATCH').always(() => {
						this.value('');
						this.saving = false;
						this.onInput();
						listo.focus(this.$title);

						hooks.doAction('collection_added', collection);
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
