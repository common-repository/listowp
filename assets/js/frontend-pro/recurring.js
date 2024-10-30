(function (listo, $) {
	const { ajax, hooks, template, data: config } = listo;

	function reload(item) {
		let { id, due } = item;

		let $item = $('[data-listo=item]').filter(`[data-id=${id}]`);
		let $recurring = $item.find('[data-listo=recurring]');

		console.log('reload');

		// Hide recurring options if due date is not set.
		if (!due) {
			$recurring.hide();
			return;
		}

		// Only take the date part.
		due = due.split(' ')[0];

		// Do not update recurring options if due date is not changed.
		let prevDue = $recurring.data('due');
		if (due === prevDue) {
			return;
		}

		return ajax('listo/v1/recurring', { date: due }).done(json => {
			render($recurring, item, json);
		});
	}

	function render(el, item, rruleOptions) {
		let recTmpl = template(config('templates.recurring'));
		let recHtml = recTmpl(Object.assign({}, item, { rrule_options: rruleOptions }));

		$(el).replaceWith(recHtml);
	}

	function submit() {
		// return ajax('listo/v1/recurring', { date: due }, 'PATCH');
	}

	hooks.addAction('item_added', reload);
	hooks.addAction('item_updated', reload);
})(window.listo, window.jQuery);
