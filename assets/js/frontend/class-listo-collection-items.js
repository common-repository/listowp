listo.class('ListoCollectionItems', function (listo, $) {
	const { ajax, hooks, template, data: config, isIOS } = listo;

	const ANIM_SPEED = +config('animation_speed');

	return class {
		constructor(container) {
			this.collection = null;

			this.$el = $(container);

			this.$el.on('click', '[data-listo=item]', e => this.onItemClick(e));
			this.$el.on('click', '[data-listo=item-toggle]', e => this.onItemToggleClick(e));
			this.$el.on('click', '[data-listo=item-check]', e => this.onItemCheckClick(e));
			this.$el.on('click', '[data-listo=description-toggle]', e =>
				this.onDescriptionToggleClick(e)
			);

			this.$el
				.on('click', '[data-listo=item-due-picker]', e => this.onItemDuePickerClick(e))
				.on('input', '[data-listo=item-due-picker]', e => this.onItemDuePickerInput(e))
				.on('change', '[data-listo=item-due-picker]', e => this.onItemDuePickerChange(e))
				.on('blur', '[data-listo=item-due-picker]', e => this.onItemDuePickerBlur(e));
			this.$el.on('click', '[data-listo=item-due-remove]', e => this.onItemDueRemoveClick(e));

			this.$el.on('click', '[data-listo=delete-item]', e => this.onDeleteItemClick(e));
			this.$el.on('click', '[data-listo=delete-item-cancel]', e =>
				this.onCancelDeleteItemClick(e)
			);
			this.$el.on('click', '[data-listo=delete-item-confirm]', e =>
				this.onConfirmDeleteItemClick(e)
			);
			this.$el.on('click', '[data-listo=edit-item]', e => this.onEditItemClick(e));
			this.$el.on('input', '[name=title]', e => this.onEditTitleInput(e));
			this.$el.on('input', '[name=description]', e => this.onEditDescriptionInput(e));
			this.$el.on('click', '[data-listo=cancel]', e => this.onCancelEditItemClick(e));
			this.$el.on('click', '[data-listo=save]', e => this.onSaveEditItemClick(e));

			hooks.addAction('set_current_collection', data => this.update(data));
		}

		update(data) {
			this.collection = data.id;

			ajax('listo/v1/items', { collection: this.collection })
				.done(json => {
					this.render(json);
					hooks.doAction('items_loaded', json);
				})
				.fail(xhr => alert(xhr.responseJSON.message));
		}

		render(items) {
			this.$el.empty();

			if (items instanceof Array && items.length) {
				let itemTmpl = template(config('templates.item'));
				items.forEach(item => {
					item.due = item.due ? item.due.split(' ')[0] : item.due;
					this.$el.append(itemTmpl(item));
				});
			} else if (!hooks.applyFilters('can_create_item')) {
				let emptyTmpl = template(config('templates.item_list_empty'));
				this.$el.append(emptyTmpl({ collection: this.collection }));
			}
		}

		loadItem(id) {
			return ajax('listo/v1/items', { id })
				.done(json => {})
				.fail(xhr => alert(xhr.responseJSON.message));
		}

		updateItem(id, data = {}) {
			return ajax('listo/v1/items', Object.assign(data, { id }), 'PATCH')
				.done(json => {
					if (json instanceof Array && json[0]) {
						hooks.doAction('item_updated', json[0]);
					}
				})
				.fail(xhr => alert(xhr.responseJSON.message));
		}

		toggleItem(item, full) {
			let $item = $(item);
			let $items = $('[data-listo=item]');
			let fullClass = 'lo-task--full';

			if (true === full && !$item.hasClass(fullClass)) {
				$items.removeClass(fullClass);
				$item.addClass(fullClass);
			} else if (false === full && $item.hasClass(fullClass)) {
				$item.removeClass(fullClass);
			} else if ('undefined' === typeof full) {
				this.toggleItem(item, !$item.hasClass(fullClass));
			}
		}

		onItemClick(e) {
			e.preventDefault();
			e.stopPropagation();

			this.toggleItem(e.currentTarget, true);
		}

		onItemToggleClick(e) {
			e.preventDefault();
			e.stopPropagation();

			let $item = $(e.currentTarget).closest('[data-listo=item]');
			this.toggleItem($item);
		}

		onItemCheckClick(e) {
			e.preventDefault();
			e.stopPropagation();

			let $item = $(e.currentTarget).closest('[data-listo=item]');
			if ($item.data('saving')) return;
			$item.data('saving', 1);

			let id = $item.data('id');
			let rrule_id = $item.data('rrule_id');
			let status = +$item.data('status') ? 0 : 1;

			this.updateItem(id, { status, rrule_id }).done(() => {
				if (rrule_id && status) {
					this.update({ id: this.collection });
					$item.removeData('saving');
					hooks.doAction('maintenance');
				} else if ('done' === this.collection && !status) {
					$item.remove();
					$item.removeData('saving');
					hooks.doAction('maintenance');
				} else {
					this.loadItem(id).done(json => {
						if (json instanceof Array && json.length) {
							let itemTmpl = template(config('templates.item'));
							let item = json[0];

							item.due = item.due ? item.due.split(' ')[0] : item.due;
							$item.replaceWith(itemTmpl(item));
						}
						$item.removeData('saving');
						hooks.doAction('maintenance');
					});
				}
			});
		}

		onItemDuePickerClick(e) {
			e.stopPropagation();

			let $picker = $(e.currentTarget);
			$picker.data('value', $picker.val());
			$picker[0].showPicker();
		}

		onItemDuePickerInput(e) {
			e.stopPropagation();

			let id = new Date().getTime();
			let $picker = $(e.currentTarget).data('date-event-id', id);

			setTimeout(() => {
				let isChanged = isIOS ? $picker.data('onblur') : $picker.data('onchange');
				let isUpdated = isChanged && $picker.data('value') !== $picker.val();

				$picker.removeData('onchange onblur');

				if (isUpdated && $picker.data('date-event-id') === id) {
					let $due = $picker.closest('[data-listo=item-due]');
					let $item = $due.closest('[data-listo=item]');
					let id = $item.data('id');
					let due = $picker.val();

					$due.show().css('opacity', 0.3);
					this.updateItem(id, { due }).done(json => {
						if (json instanceof Array && json.length) {
							let dueTmpl = template(config('templates.item_due'));
							let item = json[0];

							item.due = item.due ? item.due.split(' ')[0] : item.due;
							$due.replaceWith(dueTmpl(item));
						}

						hooks.doAction('maintenance');
					});
				}
			}, 200);
		}

		onItemDuePickerChange(e) {
			e.stopPropagation();
			$(e.currentTarget).data('onchange', true);
		}

		onItemDuePickerBlur(e) {
			e.stopPropagation();
			$(e.currentTarget).data('onblur', true);
			this.onItemDuePickerInput(e);
		}

		onItemDueRemoveClick(e) {
			e.stopPropagation();

			let $due = $(e.currentTarget).closest('[data-listo=item-due]');
			let $item = $due.closest('[data-listo=item]');

			this.updateItem($item.data('id'), { due: '' }).done(json => {
				if (json instanceof Array && json.length) {
					let dueTmpl = template(config('templates.item_due'));
					let item = json[0];

					item.due = item.due ? item.due.split(' ')[0] : item.due;
					$due.replaceWith(dueTmpl(item));
				}

				hooks.doAction('maintenance');
			});
		}

		onDeleteItemClick(e) {
			e.preventDefault();
			e.stopPropagation();

			let $item = $(e.currentTarget).closest('[data-listo=item]');

			$item.find('[data-listo=delete-item]').hide();
			$item
				.find('[data-listo=delete-item-form]')
				.add($item.find('[data-listo=delete-item-cancel]'))
				.fadeIn(ANIM_SPEED);
		}

		onCancelDeleteItemClick(e) {
			e.preventDefault();
			e.stopPropagation();

			let $item = $(e.currentTarget).closest('[data-listo=item]');

			$item
				.find('[data-listo=delete-item-form]')
				.add($item.find('[data-listo=delete-item-cancel]'))
				.fadeOut(ANIM_SPEED, () => {
					$item.find('[data-listo=delete-item]').show();
				});
		}

		onConfirmDeleteItemClick(e) {
			e.preventDefault();
			e.stopPropagation();

			let $item = $(e.currentTarget).closest('[data-listo=item]');
			let id = $item.data('id');

			return ajax(`listo/v1/items/${id}`, {}, 'DELETE')
				.done(() => {
					if ($item.siblings().length) {
						$item.css({ opacity: 0 }).slideUp(ANIM_SPEED, () => $item.remove());
					} else {
						this.render([]);
					}

					hooks.doAction('maintenance');
				})
				.fail(xhr => alert(xhr.responseJSON.message));
		}

		onDescriptionToggleClick(e) {
			e.preventDefault();
			e.stopPropagation();

			let $item = $(e.currentTarget).closest('[data-listo=item]');
			let $desc = $item.find('[data-listo=description]').parent();
			let $descToggle = $item.find('[data-listo=description-toggle]');
			let $tooltipAltClass = 'lo-tip--alt';

			$desc.slideToggle();
			$descToggle.toggleClass($tooltipAltClass);
		}

		onEditItemClick(e) {
			e.preventDefault();
			e.stopPropagation();

			let $item = $(e.currentTarget).closest('[data-listo=item]');
			let $itemCheck = $item.find('[data-listo=item-check]').parent();
			let $infoTitle = $item.find('[data-listo=title]').parent();
			let $infoDescription = $item.find('[data-listo=description]').parent();
			let $descriptionToggle = $item.find('[data-listo=description-toggle]');
			let $editItem = $item.find('[data-listo=edit-item]');
			let $editForm = $item.find('[data-listo=edit-item-form]');
			let $editTitle = $editForm.find('[name=title]');
			let $editDescription = $editForm.find('[name=description]');
			let $editSave = $editForm.find('[data-listo=save]');
			let $editClass = 'lo-task--edit';

			$item.toggleClass($editClass);
			$editItem.hide();
			$itemCheck.hide();
			$infoTitle.hide();
			$infoDescription.hide();
			$descriptionToggle.hide();
			$editForm.show();
			$editTitle.data('value', $editTitle.val());
			$editDescription.data('value', $editDescription.val()).listo_autosize();
			$editSave.attr('disabled', 'disabled');

			// Set focus on the title input.
			setTimeout(() => $editTitle[0].focus(), 100);
		}

		onEditTitleInput(e) {
			let $editForm = $(e.currentTarget).closest('[data-listo=edit-item-form]');
			let $editTitle = $editForm.find('[name=title]');
			let $editDescription = $editForm.find('[name=description]');
			let $editSave = $editForm.find('[data-listo=save]');

			if (!$editTitle.val().trim() || $editTitle.val() === $editTitle.data('value')) {
				if ($editDescription.val() === $editDescription.data('value')) {
					$editSave.attr('disabled', 'disabled');
					return;
				}
			}

			$editSave.removeAttr('disabled');
		}

		onEditDescriptionInput(e) {
			let $editForm = $(e.currentTarget).closest('[data-listo=edit-item-form]');
			let $editTitle = $editForm.find('[name=title]');
			let $editDescription = $editForm.find('[name=description]');
			let $editSave = $editForm.find('[data-listo=save]');

			if (!$editTitle.val().trim() || $editTitle.val() === $editTitle.data('value')) {
				if ($editDescription.val() === $editDescription.data('value')) {
					$editSave.attr('disabled', 'disabled');
					return;
				}
			}

			$editSave.removeAttr('disabled');
		}

		onCancelEditItemClick(e) {
			e.preventDefault();
			e.stopPropagation();

			let $item = $(e.currentTarget).closest('[data-listo=item]');
			let $itemCheck = $item.find('[data-listo=item-check]').parent();
			let $infoTitle = $item.find('[data-listo=title]').parent();
			let $infoDescription = $item.find('[data-listo=description]').parent();
			let $descriptionToggle = $item.find('[data-listo=description-toggle]');
			let $editItem = $item.find('[data-listo=edit-item]');
			let $editForm = $item.find('[data-listo=edit-item-form]');
			let $editTitle = $editForm.find('[name=title]');
			let $editDescription = $editForm.find('[name=description]');
			let $editSave = $editForm.find('[data-listo=save]');
			let $editClass = 'lo-task--edit';

			$item.toggleClass($editClass);
			$itemCheck.show();
			$editItem.show();
			$infoTitle.show();
			$infoDescription.hide();
			$descriptionToggle.show();
			$editForm.hide();
			$editTitle.val($editTitle.data('value'));
			$editDescription.val($editDescription.data('value'));
			$editSave.attr('disabled', 'disabled');
		}

		onSaveEditItemClick(e) {
			e.preventDefault();
			e.stopPropagation();

			let $item = $(e.currentTarget).closest('[data-listo=item]');
			let $editForm = $item.find('[data-listo=edit-item-form]');
			let $editTitle = $editForm.find('[name=title]');
			let $editDescription = $editForm.find('[name=description]');

			let params = {
				id: $item.data('id'),
				title: $editTitle.val().trim(),
				description: $editDescription.val().trim()
			};

			return ajax(`listo/v1/items`, params, 'PATCH')
				.done(json => {
					let itemTmpl = template(config('templates.item'));
					let item = json[0];

					item.due = item.due ? item.due.split(' ')[0] : item.due;
					$item.replaceWith(itemTmpl(item));

					hooks.doAction('maintenance');
				})
				.fail(xhr => alert(xhr.responseJSON.message));
		}
	};
});
