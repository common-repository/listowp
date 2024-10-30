(function (wp, data) {
	const { __ } = wp.i18n;
	const { createElement } = wp.element;
	const { registerBlockType } = wp.blocks;

	registerBlockType('listowp/block', {
		title: __('ListoWP', 'listowp'),
		icon: 'list-view',
		category: 'widgets',
		keywords: [__('ListoWP', 'listowp'), __('List', 'listowp')],
		edit(props) {
			return createElement('div', { class: 'listo', 'data-listo': 'root' });
		},
		save(props) {
			return null;
		}
	});
})(window.wp, window.listoEditorData);
