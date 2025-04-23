;
window.addEventListener('load', function() {
	var iworks_faq_list_buttons = document.getElementsByClassName('iworks-faq-list-toggle');
	var iworks_faq_header_buttons = document.getElementsByClassName('iworks-faq-header-toggle');
	/**
	 * toggle function
	 */
	iworks_faq_list_dd_toggle = function(element, force = null) {
		var target = document.getElementById(element.dataset.targetId);
		if ('hide' === force) {
			target.hidden = true;
			element.ariaExpanded = false;
			return;
		}
		if ('show' === force) {
			target.hidden = false;
			element.ariaExpanded = true;
			return;
		}
		if (target.hidden) {
			target.hidden = false;
			element.ariaExpanded = true;
		} else {
			target.hidden = true;
			element.ariaExpanded = false;
		}
	};
	/**
	 * global
	 */
	if (0 < iworks_faq_header_buttons.length) {
		for (var i = 0; i < iworks_faq_header_buttons.length; i++) {
			iworks_faq_header_buttons[i].addEventListener('click', function(event) {
				var elements = this.closest('aside').getElementsByClassName('iworks-faq-list-toggle');
				var force = 'false' === this.dataset.expanded ? 'show' : 'hide';
				if ('hide' === force) {
					this.dataset.expanded = 'false';
				} else {
					this.dataset.expanded = 'true';
				}
				if (0 < elements.length) {
					for (var i = 0; i < elements.length; i++) {
						iworks_faq_list_dd_toggle(elements[i], force);
					}
				}
			});
		}
	}
	/**
	 * bind Menu click
	 */
	if (0 < iworks_faq_list_buttons.length) {
		for (var i = 0; i < iworks_faq_list_buttons.length; i++) {
			iworks_faq_list_buttons[i].addEventListener('click', function(event) {
				event.preventDefault();
				iworks_faq_list_dd_toggle(this);
				return false;
			});
		}
	}
}, {
	passive: true
});