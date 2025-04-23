;
window.addEventListener('load', function() {
	var iworks_slider_z_index = 10;
	var iworks_slider_slider_timeout = 8000;
	var iworks_slider_main_slider = document.getElementById('main-slider');
	var iworks_slider_slider_menu;
	var iworks_slider_slider_elements;
	var iworks_slider_current_index = -1;
	if (null === iworks_slider_main_slider) {
		return;
	}
	iworks_slider_slider_menu = iworks_slider_main_slider.getElementsByTagName('nav');
	if (null === iworks_slider_slider_menu) {
		return;
	}
	iworks_slider_slider_menu = iworks_slider_slider_menu[0];
	iworks_slider_slider_elements = iworks_slider_slider_menu.getElementsByTagName('li');
	if (1 > iworks_slider_slider_elements.length) {
		return;
	}
	/**
	 * Switch to helper
	 */
	function iworks_slider_slider_switch_to(index) {
		var li = iworks_slider_slider_elements[index];
		var el = li.getElementsByTagName('a')[0];
		var id = el.dataset.id;
		var articles = iworks_slider_main_slider.getElementsByTagName('article');
		var article = document.getElementById('post-' + id);
		/**
		 * set menu
		 */
		for (var i = 0; i < iworks_slider_slider_elements.length; i++) {
			iworks_slider_slider_elements[i].classList.remove('active');
		}
		li.classList.add('active');
		/**
		 * set article
		 */
		for (var i = 0; i < articles.length; i++) {
			articles[i].classList.remove('active');
		}
		article.classList.add('active');
		article.style.zIndex = iworks_slider_z_index;
		/**
		 * increment
		 */
		iworks_slider_z_index++;
		clearTimeout(window.iworks_slider_slider_timeout_object);
		window.iworks_slider_slider_timeout_object = setTimeout(window.iworks_slider_slider, iworks_slider_slider_timeout);
	}
	/**
	 * handle autoswitch
	 */
	window.iworks_slider_slider = function() {
		/**
		 * sanitize index
		 */
		if (0 > parseInt(iworks_slider_current_index)) {
			iworks_slider_current_index = 1;
		}
		if (iworks_slider_current_index >= iworks_slider_slider_elements.length) {
			iworks_slider_current_index = 0;
		}
		/**
		 * run Forest!
		 */
		iworks_slider_slider_switch_to(iworks_slider_current_index++);
	};
	/**
	 * first run
	 */
	window.iworks_slider_slider_timeout_object = setTimeout(iworks_slider_slider, iworks_slider_slider_timeout);
	/**
	 * attach class
	 */
	iworks_slider_slider_elements[0].classList.add('active');
	/**
	 * bind Menu click
	 */
	for (var i = 0; i < iworks_slider_slider_elements.length; i++) {
		iworks_slider_slider_elements[i].getElementsByTagName('a')[0].addEventListener('click', function(event) {
			event.preventDefault();
			iworks_slider_slider_switch_to(this.dataset.index);
			return false;
		});
	}
}, {
	passive: true
});