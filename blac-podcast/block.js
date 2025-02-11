(function (wp) {
	console.log('Block script loaded');

	function registerBlock() {
		if (!window.wp || !window.wp.blocks) {
			console.error('WordPress blocks are not available.');
			return;
		}
		window.wp.blocks.registerBlockType('custom/podcasts-list', {
			title: 'BLAC Buzzsprout Podcasts',
			icon: 'microphone',
			category: 'widgets',
			edit: function () {
				// Placeholder for the editor view.
				return window.wp.element.createElement(
					'p',
					null,
					'Podcast block preview'
				);
			},
			save: function () {
				// Dynamic block – content rendered via PHP.
				return null;
			},
		});
	}

	// If wp.domReady exists, use it; otherwise, fall back to DOMContentLoaded.
	if (wp && wp.domReady) {
		wp.domReady(registerBlock);
	} else {
		console.warn(
			'wp.domReady is not available, falling back to DOMContentLoaded.'
		);
		document.addEventListener('DOMContentLoaded', registerBlock);
	}
})(window.wp);
