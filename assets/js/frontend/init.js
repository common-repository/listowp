(function (listo, listoData, $, wp = {}) {
	if (!+listoData.logged_in) {
		return;
	}

	function initListoWP() {
		document.querySelectorAll('[data-listo=root]').forEach(el => {
			new (listo.class('ListoRoot'))(el);
		});
	}

	$(function () {
		if (wp && wp.domReady) {
			wp.domReady(() => setTimeout(initListoWP, 0));
		} else {
			initListoWP();
		}
	});
})(window.listo, window.listoData, window.jQuery, window.wp);
