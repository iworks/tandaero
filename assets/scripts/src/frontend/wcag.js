/**
 * Remove focus on ESC key
 */
document.addEventListener('keydown', function(e) {
	if ('Escape' === e.key) {
		document.activeElement.blur();
	}
});