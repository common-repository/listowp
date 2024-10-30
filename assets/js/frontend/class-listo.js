listo.class('Listo', function (listo, $) {
	const { ajax, hooks, template, data: config, escapeHtml, nl2br } = listo;

	const ENABLE_COLLECTIONS = !!+config('enable_collections');

	return class {
		constructor(container) {
			this.collectionsMap = {};
			this.currentCollection = config('last_viewed_collection');

			let rootTemplate = template(config('templates.root'));
			let rootTemplateData = { enable_collections: ENABLE_COLLECTIONS, lang: config('lang') };

			this.$el = $(container).html(rootTemplate(rootTemplateData));
			this.$wrapper = this.$el.find('[data-listo=wrapper]');
			this.$sidebar = this.$el.find('[data-listo=sidebar]');
			this.$smartCollections = this.$el.find('[data-listo=smart-collections]');
			this.$collections = this.$el.find('[data-listo=collections]');
			this.$collectionHeader = this.$el.find('[data-listo=collection-header]');
			this.$items = this.$el.find('[data-listo=items]');
			this.$panels = this.$el.find('[data-listo=panel]');
			this.$timezone = this.$el.find('[data-listo=user-timezone]');
			this.$preferences = this.$el.find('[data-listo=preferences]');

			this.$el.on('click change', '[data-listo]', e => this.triggerEventListener(e));
			this.$el.on('click', '[data-listo=pref-toggle]', e => this.$preferences.fadeToggle(200));
			this.$el.on('click', '[data-listo=pref-close]', e => this.$preferences.fadeToggle(200));

			// Initialize collection header.
			new (listo.class('ListoCollectionHeader'))(this.$collectionHeader);

			// Initialize collection items.
			new (listo.class('ListoCollectionItems'))(this.$items);

			// Initialize new collection form.
			new (listo.class('ListoNewCollection'))({
				toggle: this.$el.find('[data-listo=new-collection]'),
				form: this.$el.find('[data-listo=new-collection-form]')
			});

			// Initialize new item form.
			new (listo.class('ListoNewItem'))({
				toggle: this.$el.find('[data-listo=new-item]'),
				form: this.$el.find('[data-listo=new-item-form]')
			});

			// Initialize sortable.
			this.sortable = new (listo.class('ListoSortable'))(this.$el);

			hooks.addFilter('current_collection_id', () => this.currentCollection);
			hooks.addFilter('can_create_item', collection => this.canCreateItem(collection));

			hooks.addAction('collection_added', data => this.onCollectionAdded(data));
			hooks.addAction('collection_updated', data => this.onCollectionUpdated(data));
			hooks.addAction('collection_deleted', id => this.onCollectionDeleted(id));
			hooks.addAction('items_loaded', () => this.onItemsLoaded());
			hooks.addAction('item_added', data => this.onItemAdded(data));
			hooks.addAction('item_moved', data => this.onItemMoved(data));

			hooks.addAction('maintenance', () => this.maintenance());

			$(document).on('keydown', e => {
				if ('KeyF' === e.code && e.ctrlKey && !e.altKey && !e.shiftKey) {
					this.onWrapperToggleClick(e);
				}
			});

			this.maintenance().always(() => {
				if (ENABLE_COLLECTIONS) {
					this.loadCollections();
					this.getTimezone();
				} else {
					this.loadItems('all');
					this.getTimezone();
				}
			});
		}

		$collection(id) {
			return this.$smartCollections
				.add(this.$collections)
				.children(`[data-listo=collection][data-id=${id}]`);
		}

		$item(id) {
			return this.$items.children(`[data-listo=item][data-id=${id}]`);
		}

		loadCollections() {
			this.$panels.addClass('loading');

			return ajax('listo/v1/collections')
				.done(json => {
					this.collectionsMap = {};

					this.$smartCollections.empty();
					this.$collections.empty();

					if (json instanceof Array) {
						// Sort collections based on the "order" key.
						json.sort((a, b) => +a.order - +b.order);

						let collectionTemplate = template(config('templates.collection'));

						json.forEach(collection => {
							this.collectionsMap[collection.id] = collection;

							let html = collectionTemplate(collection);

							if (collection.smart > 0) {
								this.$smartCollections.append(html);
							} else {
								this.$collections.append(html);
							}
						});

						this.loadItems(this.currentCollection);
					}

					hooks.doAction('collections_loaded');
				})
				.fail(xhr => alert(xhr.responseJSON.message))
				.always(() => {
					this.$panels.removeClass('loading');
				});
		}

		loadItems(collection) {
			this.$panels.addClass('loading');
			this.currentCollection = collection;

			let $collections = this.$el.find('[data-listo=collection]');
			let $collection = this.$collection(collection);

			// Toggle active class for current collection.
			$collections.filter('.active').not($collection).removeClass('active');
			$collection.addClass('active');

			hooks.doAction('set_current_collection', this.collectionsMap[collection]);
		}

		loadItem(id) {
			return ajax('listo/v1/items', { id })
				.done(json => {})
				.fail(xhr => alert(xhr.responseJSON.message));
		}

		updateItem(id, data = {}) {
			return ajax('listo/v1/items', Object.assign(data, { id }), 'PATCH')
				.done(json => {})
				.fail(xhr => alert(xhr.responseJSON.message));
		}

		canCreateItem(collection) {
			collection = collection || this.currentCollection;
			return ['due', 'scheduled', 'done', 'recurring'].indexOf(collection) === -1;
		}

		getTimezone() {
			return ajax('listo/v1/preferences', { id: 'timezone' }).done(json => {
				if (json.timezone) {
					this.$timezone.val(json.timezone);
				}
			});
		}

		setTimezone(value) {
			return ajax('listo/v1/preferences', { id: 'timezone', value }, 'PATCH');
		}

		maintenance() {
			return ajax('listo/v1/maintenance').done(json => {
				if (json instanceof Array && json[0] instanceof Object) {
					for (const id in json[0]) {
						let data = json[0][id];
						let $fields = this.$collection(id).find('[data-field]');

						$fields.each((i, el) => {
							let $field = $(el);
							let field = $field.data('field');
							let count = data[`${field}_formatted`];

							$field.html(count);
							if ('count_items_due' === field) {
								+count ? $field.show() : $field.hide();
							}
						});

						hooks.doAction('count_items_updated', Object.assign(data, { id }));
					}
				}
			});
		}

		triggerEventListener(e) {
			let elementId = e.currentTarget.getAttribute('data-listo');
			let eventListener = `on${capitalize(elementId + '-' + e.type)}`;

			if ('function' === typeof this[eventListener]) {
				this[eventListener](e);
			}
		}

		onWrapperToggleClick(e) {
			e.preventDefault();
			e.stopPropagation();

			this.$wrapper.toggleClass('lo-wrapper--fullscreen');
		}

		onSidebarToggleClick(e) {
			e.preventDefault();
			e.stopPropagation();

			if (this.$sidebar.hasClass('lo-sidebar--expanded')) {
				$(document).off('click.listo-sidebar');
				this.$sidebar.removeClass('lo-sidebar--expanded');
			} else {
				this.$sidebar.addClass('lo-sidebar--expanded');
				$(document).one('click.listo-sidebar', () => {
					this.$sidebar.removeClass('lo-sidebar--expanded');
				});
			}
		}

		onCollectionAdded(data = {}) {
			if ('object' === typeof data && data.id) {
				this.currentCollection = data.id;
				this.loadCollections();
			}
		}

		onCollectionUpdated(data = {}) {
			if ('object' === typeof data && data.id) {
				let $collection = this.$collection(data.id);

				// Update collection information.
				for (const key in data) {
					let value = data[key];

					// Escape HTML tags and replace newlines with '<br>' tags.
					if (['initials', 'title', 'description'].indexOf(key) > -1) {
						value = nl2br(escapeHtml(value));
					}

					$collection.find(`[data-field=${key}]`).html(value);
				}
			}
		}

		onCollectionDeleted(id) {
			this.$collection(id).remove();
			this.currentCollection = 'inbox';
			this.loadCollections();
		}

		onCollectionClick(e) {
			e.preventDefault();

			let $collection = $(e.currentTarget);

			this.loadItems($collection.data('id'));
		}

		onItemsLoaded() {
			this.sortable.init(this.currentCollection);
		}

		onItemAdded(data = {}) {
			if ('object' === typeof data && data.id) {
				hooks.doAction('maintenance');
				this.loadItems(this.currentCollection);
			}
		}

		onItemMoved(data = {}) {
			let $remaining = this.$items.children('[data-listo=item]');
			if (!$remaining.length && !hooks.applyFilters('can_create_item')) {
				let emptyTmpl = template(config('templates.item_list_empty'));
				let emptyHtml = emptyTmpl({ collection: this.currentCollection });
				this.$items.append(emptyHtml);
			}

			hooks.doAction('maintenance');
		}

		onUserTimezoneChange(e) {
			this.setTimezone(e.currentTarget.value).done(() => {
				ENABLE_COLLECTIONS ? this.loadCollections() : this.loadItems('all');
			});
		}

		onGdprDeleteClick(e) {
			e.preventDefault();
			e.stopPropagation();

			if (!confirm(config('lang.gdpr_delete_confirmation'))) {
				return;
			}

			return ajax('listo/v1/gdpr', {}, 'DELETE')
				.done(json => {
					this.currentCollection = 'inbox';
					this.$timezone.val('+00:00');
					ENABLE_COLLECTIONS ? this.loadCollections() : this.loadItems('all');
				})
				.fail(xhr => alert(xhr.responseJSON.message));
		}

		onDropdownToggleClick(e) {
			let $toggle = $(e.currentTarget);
			let $menu = $toggle.siblings('[data-listo=dropdown-menu]');

			if ($menu.is(':hidden')) {
				e.stopPropagation();
				$menu.show();
				$(document).one('click', () => $menu.hide());
			} else {
				$menu.hide();
			}
		}

		onDropdownMenuClick(e) {
			e.stopPropagation();
		}
	};

	function capitalize(str) {
		return str.replace(/(?:^|-)([a-z])/gi, function (match, letter) {
			return letter.toUpperCase();
		});
	}
});
