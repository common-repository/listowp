listo.class('ListoCollectionHeader', function (listo, $) {
	const { ajax, hooks, data: config } = listo;
	const { escapeHtml, nl2br } = listo.string;

	const ANIM_SPEED = +config('animation_speed');

	return class extends listo.class('ListoAbstract') {
		constructor(container) {
			super(container);

			this.data = null;
			this.saving = false;

			this.$el = $(container);
			this.$info = this.$el.find('[data-listo=collection-info]');
			this.$infoTitle = this.$info.find('[data-listo=title]');
			this.$infoDescription = this.$info.find('[data-listo=description]');
			this.$infoDescriptionToggle = this.$info.find('[data-listo=description-toggle]');
			this.$edit = this.$el.find('[data-listo=edit-collection]');
			this.$editForm = this.$el.find('[data-listo=edit-collection-form]');
			this.$editTitle = this.$editForm.find('[name=title]');
			this.$editDescription = this.$editForm.find('[name=description]');
			this.$editCancel = this.$editForm.find('[data-listo=cancel]');
			this.$editSave = this.$editForm.find('[data-listo=save]');
			this.$delete = this.$el.find('[data-listo=delete-collection]');
			this.$deleteForm = this.$el.find('[data-listo=delete-collection-form]');
			this.$deleteCancel = this.$el.find('[data-listo=delete-collection-cancel]');
			this.$deleteConfirm = this.$el.find('[data-listo=delete-collection-confirm]');
			this.$createItem = $('[data-listo="wrapper"]').find('[data-listo=new-item]');

			// Initialize custom icon behavior.
			this.$el.find('[data-listo=custom-icon]').each((i, el) => {
				new (listo.class('ListoCustomIcons'))(el);
			});

			this.$infoDescriptionToggle.on('click', e => this.onInfoDescriptionToggleClick(e));
			this.$edit.on('click', e => this.onEditClick(e));
			this.$editTitle.on('input', e => this.onEditTitleInput(e));
			this.$editDescription.on('input', e => this.onEditDescriptionInput(e));
			this.$editCancel.on('click', e => this.onEditCancelClick(e));
			this.$editSave.on('click', e => this.onEditSaveClick(e));
			this.$delete.on('click', e => this.onDeleteClick(e));
			this.$deleteCancel.on('click', e => this.onDeleteCancelClick(e));
			this.$deleteConfirm.on('click', e => this.onDeleteConfirmClick(e));

			hooks.addAction('set_current_collection', data => this.update(data));
			hooks.addAction('count_items_updated', data => this.updateCountItems(data));

			hooks.addAction('collections_loaded', 'new_item_form', () => {
				hooks.removeAction('collections_loaded', 'new_item_form');
				this.$el.show();
			});
		}

		reset() {
			let title = this.data.title.trim();
			let description = this.data.description.trim();
			let smart = +this.data.smart;

			this.$info.show();
			this.$info.attr('data-id', this.data.id);
			this.$infoTitle.html(nl2br(escapeHtml(title)));
			this.$infoDescription.html(nl2br(escapeHtml(description)));
			this.$edit[smart ? 'hide' : 'show']();
			this.$editForm.hide();
			this.$editTitle.val(title);
			this.$editDescription.val(description);
			this.$editCancel.removeAttr('disabled');
			this.$editSave.attr('disabled', 'disabled');
			this.$delete.parent()[smart ? 'fadeOut' : 'fadeIn'](ANIM_SPEED);
			this.$delete.show();
			this.$deleteForm.hide();
			this.$deleteCancel.hide();
			this.$createItem.show();

			// Show description toggle if necessary.
			let desc = this.$infoDescription[0];
			if (desc.scrollHeight > desc.clientHeight * 1.5) {
				this.$infoDescriptionToggle.show();
			} else {
				this.$infoDescriptionToggle.hide();
			}

			this.updateCountItems(this.data);
		}

		update(data) {
			this.data = data;
			this.reset();
		}

		updateCountItems(data = {}) {
			if (data.id === this.data.id) {
				this.$info.find('[data-field=count_items]').html(data.count_items);
				this.$info.find('[data-field=count_items_done]').html(data.count_items_done);
				this.$info.find('[data-field=count_items_due]').html(data.count_items_due);
			}
		}

		onInfoDescriptionToggleClick(e) {
			e.preventDefault();
			e.stopPropagation();

			let desc = this.$infoDescription[0];
			let expanded = desc.style.height === 'auto';
			let css = { height: expanded ? '' : 'auto', overflow: expanded ? '' : 'visible' };

			this.$infoDescription.css(css);
			this.$infoDescriptionToggle[expanded ? 'removeClass' : 'addClass']('lo-tip--alt');
		}

		onToggleCompletedClick(e) {
			e.preventDefault();
			e.stopPropagation();

			if ('done' !== this.data.id) {
				hooks.doAction('toggle_completed');
			}
		}

		onEditClick(e) {
			e.preventDefault();
			e.stopPropagation();

			this.$info.hide();
			this.$edit.hide();
			this.$editForm.show();
			this.$editTitle.data('value', this.$editTitle.val());
			this.$editDescription.data('value', this.$editDescription.val()).listo_autosize();
			this.$editSave.attr('disabled', 'disabled');
			//this.$delete.parent().fadeOut(ANIM_SPEED);
			this.$createItem.fadeOut(ANIM_SPEED);

			// Set focus on the title input.
			listo.focus(this.$editTitle);
		}

		onEditTitleInput(e) {
			let title = this.$editTitle.val();
			if (!title.trim() || title === this.$editTitle.data('value')) {
				if (this.$editDescription.val() === this.$editDescription.data('value')) {
					this.$editSave.attr('disabled', 'disabled');
					return;
				}
			}

			this.$editSave.removeAttr('disabled');
		}

		onEditDescriptionInput(e) {
			let title = this.$editTitle.val();
			if (!title.trim() || title === this.$editTitle.data('value')) {
				if (this.$editDescription.val() === this.$editDescription.data('value')) {
					this.$editSave.attr('disabled', 'disabled');
					return;
				}
			}

			this.$editSave.removeAttr('disabled');
		}

		onEditCancelClick(e) {
			e.preventDefault();
			e.stopPropagation();

			this.reset();
		}

		onEditSaveClick(e) {
			e.preventDefault();
			e.stopPropagation();

			if (this.saving) return;
			this.saving = true;
			this.$editCancel.add(this.$editSave).attr('disabled', 'disabled');

			let id = this.data.id;
			let title = this.$editTitle.val().trim();
			let description = this.$editDescription.val().trim();
			let params = { id, title, description };

			ajax('listowp/v1/collections', params, 'PATCH').done(json => {
				Object.assign(this.data, params); // temporary, or if GET request failed
				ajax('listowp/v1/collections', { id })
					.done(json => {
						if (json instanceof Array && json[0]) {
							this.data = json[0];
						}
					})
					.always(() => {
						this.saving = false;
						this.reset();
						hooks.doAction('collection_updated', this.data);
					});
			});
		}

		onDeleteClick(e) {
			e.preventDefault();
			e.stopPropagation();

			this.$delete.hide();
			this.$deleteForm.add(this.$deleteCancel).fadeIn(ANIM_SPEED);
		}

		onDeleteCancelClick(e) {
			e.preventDefault();
			e.stopPropagation();

			this.$deleteForm.add(this.$deleteCancel).fadeOut(ANIM_SPEED, () => this.$delete.show());
		}

		onDeleteConfirmClick(e) {
			e.preventDefault();
			e.stopPropagation();

			let $indicator = this.$deleteCancel.find('i');
			let $disabler = $('<div/>').css({
				position: 'absolute',
				top: 0,
				left: 0,
				right: 0,
				bottom: 0,
				background: 'rgba(255,255,255,0.5)'
			});

			$disabler.appendTo(this.$el);
			$indicator.attr('class', 'fa-solid fa-circle-notch fa-spin');
			ajax(`listowp/v1/collections/${this.data.id}`, {}, 'DELETE')
				.always(() => {
					$disabler.remove();
					$indicator.attr('class', 'fa-solid fa-xmark');
				})
				.done(json => {
					this.$deleteForm.add(this.$deleteCancel).hide();
					this.$delete.show();

					hooks.doAction('collection_deleted', this.data.id);
				});
		}

		onCreateItemClick(e) {}
	};
});
