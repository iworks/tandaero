document.addEventListener("DOMContentLoaded", function(event) {
	var links = document.getElementsByTagName('a');
	var re = new RegExp( window.iworks_theme.home_url, 'i' );
	for (i = 0; i < links.length; i++) {
		var link = links[i];
		if (
			link.href.match( /^https?:/ )
			&& ! link.href.match(re)
		) {
			link.classList.add('external');
		}
	}
});

