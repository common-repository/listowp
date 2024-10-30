listo.class('ListoSortable', function (listo, $) {
	const { ajax, hooks, data: config } = listo;

	const TOUCH_HOLD_WAIT = 400; // In milliseconds.
	const ANIM_SPEED = +config('animation_speed') || 0;

	return class {
		constructor(container) {
			this.currentCollection = null;
			this.manualItemsOrder = null;

			this.container = container.jquery ? container[0] : container;
			this.smartCollections = this.container.querySelector('[data-listo=smart-collections]');
			this.collections = this.container.querySelector('[data-listo=collections]');
			this.items = this.container.querySelector('[data-listo=items]');

			this.container.addEventListener('dragstart', e => this.onDragStart(e));
			this.container.addEventListener('dragend', e => this.onDragEnd(e));
			this.container.addEventListener('dragenter', e => this.onDragEnter(e));
			this.container.addEventListener('dragover', e => e.preventDefault());
			this.container.addEventListener('dragleave', e => this.onDragLeave(e));
			this.container.addEventListener('drop', e => this.onDrop(e));

			// Support touch devices.
			if ('ontouchstart' in window) {
				this.container.addEventListener('touchstart', e => this.onTouchStart(e));
				this.container.addEventListener('touchmove', e => this.onTouchMove(e));
				this.container.addEventListener('touchend', e => this.onTouchEnd(e));
				this.container.addEventListener('touchcancel', e => this.onTouchCancel(e));
				this.container.addEventListener('contextmenu', e => e.preventDefault());
			}

			hooks.addAction('item_added', () => this.onItemAdded());
			hooks.addAction('item_updated', () => this.onItemUpdated());
			hooks.addAction('preferences_init', prefs => this.onPreferencesUpdated(prefs));
			hooks.addAction('preferences_updated', prefs => this.onPreferencesUpdated(prefs));
		}

		init(currentCollection) {
			this.currentCollection = String(currentCollection);

			let collections = this.collections.querySelectorAll('[data-listo=collection]');
			let items = this.items.querySelectorAll('[data-listo=item]');
			let draggables = Array.from(collections).concat(Array.from(items));

			draggables.forEach(el => {
				el.getAttribute('draggable') || el.setAttribute('draggable', 'true');
			});
		}

		reorderCollections() {
			let collections = this.collections.querySelectorAll('[data-listo=collection]');
			let ids = Array.from(collections).map(el => el.getAttribute('data-id'));

			return ajax('listowp/v1/collections', { ids }, 'PATCH').done(() => {
				hooks.doAction('collections_reorder');
			});
		}

		reorderItems() {
			let items = this.items.querySelectorAll('[data-listo=item]');
			let ids = Array.from(items).map(el => el.getAttribute('data-id'));
			let collection = this.currentCollection;

			return ajax('listowp/v1/items', { ids, collection }, 'PATCH').done(() => {
				hooks.doAction('items_reorder');
			});
		}

		moveItem(id, collection) {
			collection = collection > 0 ? collection : 0;

			return ajax('listowp/v1/items', { id, collection }, 'PATCH').done(() => {
				hooks.doAction('item_moved', { id, collection });
			});
		}

		droppable(source, target) {
			if (!source || source === target) {
				return false;
			}

			let sourceType = source.getAttribute('data-listo');
			let targetType = target.getAttribute('data-listo');

			if (sourceType === 'collection') {
				if (targetType === 'collection' && +target.getAttribute('data-smart')) return false;
				if (targetType === 'item') return false;
			} else if (sourceType === 'item') {
				if (targetType === 'collection') {
					let collection = target.getAttribute('data-id');
					if (collection === this.currentCollection) return false;
					if (!collection.match(/^(\d+|inbox)$/)) return false;
				} else if (targetType === 'item') {
					if (!this.currentCollection.match(/^(\d+)$/)) return false;
					if (!this.manualItemsOrder) return false;
				}
			}

			return true;
		}

		onDragStart(e) {
			if (!this.dragEl) {
				this.dragEl = closest(e.target, '[draggable]');
				this.dragImage = clone(this.dragEl);
				e.dataTransfer.setDragImage(this.dragImage, e.offsetX, e.offsetY);
			}
		}

		onDragEnd(e) {
			if (this.dragEl) {
				this.dragEl = null;
				this.dragImage.remove();
				this.dragImage = null;
				this.dropTarget = null;
				this.container.querySelectorAll('.lo-droptarget').forEach(el => {
					el.classList.remove('lo-droptarget');
				});
			}
		}

		onDragEnter(eventOrElem) {
			let target = eventOrElem;
			if (eventOrElem.target) {
				target = closest(eventOrElem.target, '[data-listo=collection], [data-listo=item]');
			}

			this.dropTarget = null;
			if (target && this.droppable(this.dragEl, target)) {
				this.dropTarget = target;
				target.classList.add('lo-droptarget');
			}
		}

		onDragLeave(eventOrElem) {
			let target = eventOrElem;
			if (eventOrElem.target) {
				target = closest(eventOrElem.target, '[data-listo=collection], [data-listo=item]');
			}

			if (target && target !== this.dropTarget) {
				target.classList.remove('lo-droptarget');
			}
		}

		onDrop(e) {
			if (e instanceof Event) {
				e.preventDefault();
				e.stopPropagation();
			}

			let source = this.dragEl;
			let target = this.dropTarget;
			if (!source || !target || source === target) {
				return;
			}

			let sourceType = source.getAttribute('data-listo');
			let targetType = target.getAttribute('data-listo');
			let sourceId = source.getAttribute('data-id');
			let targetId = target.getAttribute('data-id');

			if (sourceType === 'item' && targetType === 'collection') {
				this.moveItem(sourceId, targetId);
				$(source)
					.css({ opacity: 0, transition: 'none' })
					.slideUp(ANIM_SPEED, () => source.remove());
			} else if (sourceType === targetType) {
				let parentElement = source.parentElement;

				if (index(source) > index(target)) {
					parentElement.insertBefore(source, target);
				} else {
					let nextSibling = target.nextElementSibling;
					if (nextSibling) {
						parentElement.insertBefore(source, nextSibling);
					} else {
						parentElement.append(source);
					}
				}

				if ('collection' === sourceType) {
					this.reorderCollections();
				} else if ('item' === sourceType) {
					this.reorderItems();
				}
			}
		}

		onTouchStart(e) {
			this.holdTimer = setTimeout(() => {
				if (typeof DataTransfer === 'function') {
					e.dataTransfer = new DataTransfer();
				} else {
					e.dataTransfer = { setDragImage() {} };
				}

				this.onDragStart(e);

				let touch = e.touches[0] || e.changedTouches[0];
				this.touchPageX = touch.pageX;
				this.touchPageY = touch.pageY;
			}, TOUCH_HOLD_WAIT);
		}

		onTouchMove(e) {
			clearTimeout(this.holdTimer);

			if (this.dragEl) {
				e.preventDefault();
				e.stopPropagation();

				let touch = e.touches[0] || e.changedTouches[0];
				let deltaX = touch.pageX - this.touchPageX;
				let deltaY = touch.pageY - this.touchPageY;
				this.dragImage.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
				this.dragImage.style.zIndex = 100;

				this.dragImage.style.display = 'none';
				let target = document.elementFromPoint(touch.clientX, touch.clientY);
				this.dragImage.style.display = '';
				target = closest(target, '[data-listo=collection], [data-listo=item]');
				if (target !== this.dropTarget) {
					this.dropTarget && this.dropTarget.classList.remove('lo-droptarget');
					if (target && this.droppable(this.dragEl, target)) {
						target.classList.add('lo-droptarget');
					}
					this.dropTarget = target;
				}
			}
		}

		onTouchEnd(e) {
			clearTimeout(this.holdTimer);

			this.onDrop();
			this.onDragEnd();
		}

		onTouchCancel(e) {
			clearTimeout(this.holdTimer);
		}

		onItemAdded() {
			this.init(this.currentCollection);
		}

		onItemUpdated() {
			this.init(this.currentCollection);
		}

		onPreferencesUpdated(prefs) {
			this.manualItemsOrder = !prefs.items_order;
			this.init(this.currentCollection);
		}
	};

	function clone(el) {
		let pos = el.getBoundingClientRect();
		let clone = el.cloneNode(true);

		clone.removeAttribute('id');
		clone.removeAttribute('data-listo');
		clone.style.position = 'fixed';
		clone.style.top = `${pos.top}px`;
		clone.style.left = `${pos.left}px`;
		clone.style.width = `${pos.width}px`;
		clone.style.height = `${pos.height}px`;
		clone.style.zIndex = -1;
		clone.classList.add('lo-dragged');

		el.parentElement.append(clone);

		return clone;
	}

	function closest(el, selector) {
		for (; el; el = el.parentElement) {
			if (el.matches(selector)) {
				return el;
			}
		}

		return null;
	}

	function index(el, idx = 0) {
		if (el.previousElementSibling) {
			idx = index(el.previousElementSibling, idx + 1);
		}

		return idx;
	}
});
