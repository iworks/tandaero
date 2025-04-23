;
window.addEventListener('load', function() {
	var head = document.getElementsByTagName('head')[0];
	var link = document.createElement('link');
	link.href = '//fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&family=Crimson+Pro:wght@300;400;600&display=swap&subset=latin-ext';
	link.rel = 'stylesheet';
	head.append(link);
	document.body.classList.add('fonts-added');
}, {
	passive: true
});