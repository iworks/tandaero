document.addEventListener("DOMContentLoaded", function(event) {
	var value, cookie_container, iworks_cookie_xml_http;
	/**
	 * check enviroment
	 */
	if ('undefined' === typeof window.iworks_cookie) {
		return;
	}
	if ('undefined' === typeof window.iworks_cookie.name) {
		return;
	}
	cookie_container = document.getElementById(window.iworks_cookie.name);
	iworks_cookie_xml_http = new XMLHttpRequest();
	/**
	 * get cookie value
	 */
	var iWorksCookieGetCookieValue = function(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	};
	/**
	 * set Cookie Notice
	 */
	var iWorksCookieSetCookieNotice = function() {
		var expires = new Date();
		var value = parseInt(expires.getTime());
		var cookie = '';
		var data;
		var query = '';
		/**
		 * set time
		 */
		value = parseInt(expires.getTime());
		/**
		 * add time
		 */
		value += parseInt(window.iworks_cookie.cookie.value) * 1000;
		/**
		 * add time zone
		 */
		value += parseInt(window.iworks_cookie.cookie.timezone) * 1000;
		/**
		 * set time
		 */
		expires.setTime(value + 2 * 24 * 60 * 60 * 1000);
		/**
		 * add cookie timestamp
		 */
		cookie = window.iworks_cookie.cookie.name + '=' + value / 1000 + ';';
		cookie += ' expires=' + expires.toUTCString() + ';';
		if (window.iworks_cookie.cookie.domain) {
			cookie += ' domain=' + window.iworks_cookie.cookie.domain + ';';
		}
		/**
		 * Add cookie now (fix cache issue)
		 */
		cookie += ' path=' + window.iworks_cookie.cookie.path + ';';
		if ('on' === window.iworks_cookie.cookie.secure) {
			cookie += ' secure;';
		}
		document.cookie = cookie;
		cookie = window.iworks_cookie.cookie.name + '_close=hide;';
		cookie += ' expires=;';
		if (window.iworks_cookie.cookie.domain) {
			cookie += ' domain=' + window.iworks_cookie.cookie.domain + ';';
		}
		cookie += ' path=' + window.iworks_cookie.cookie.path + ';';
		if ('on' === window.iworks_cookie.cookie.secure) {
			cookie += ' secure;';
		}
		document.cookie = cookie;
		/**
		 * set user meta
		 */
		if (undefined !== window.iworks_cookie.cookie.logged && 'yes' === window.iworks_cookie.cookie.logged) {
			data = {
				'action': 'iworks_cookie_notice',
				'user_id': window.iworks_cookie.cookie.user_id,
				'nonce': window.iworks_cookie.cookie.nonce
			};
		} else {
			// Dimiss the notice for visitor.
			data = {
				'action': 'iworks_cookie_notice',
				'nonce': window.iworks_cookie.cookie.nonce
			};
		}
		/**
		 * send data
		 */
		for (var key in data) {
			if (query.length) {
				query += '&';
			}
			query += encodeURIComponent(key);
			query += '=';
			query += encodeURIComponent(data[key]);
		}
		iworks_cookie_xml_http.open('GET', window.iworks_cookie.cookie.ajaxurl + '?' + query, true);
		iworks_cookie_xml_http.send(null);
		/**
		 * hide
		 */
		cookie_container.style.display = 'none';
	};
	/**
	 * bind
	 */
	cookie_container.getElementsByClassName('button')[0].addEventListener('click', function(e) {
		e.preventDefault();
		iWorksCookieSetCookieNotice();
		return false;
	});
	/**
	 * it ws already shown
	 */
	value = iWorksCookieGetCookieValue(window.iworks_cookie.cookie.name + '_close');
	if ('hide' === value) {
		cookie_container.style.display = 'none';
	}
});