(function ($) {
	function autosize(textarea) {
		textarea.style.height = '';
		textarea.style.height = +textarea.scrollHeight + 'px';
	}

	$.fn.listo_autosize = function () {
		return this.each(function () {
			autosize(this);
			$(this)
				.off('input.listo-autosize')
				.on('input.listo-autosize', function () {
					autosize(this);
				});
		});
	};
})(window.jQuery);
