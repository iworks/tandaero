/* global document */
document.addEventListener("DOMContentLoaded", function(event) {
	document.getElementById('iworks-phone-section').addEventListener('click', function(event) {
		document.location.href = 'tel:';
	});
});