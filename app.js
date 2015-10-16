
window.addEventListener('load', function(e) {

	var links = [].slice.call(document.querySelectorAll('a.activate-feed'));
	links.forEach(function(link) {
		link.addEventListener('click', function(e) {
			e.preventDefault();

			var url = this.getAttribute('href'),
				name = prompt('Blog name..?', '');

			if (name) {
				location = url + encodeURIComponent(name);
			}
		});
	});

});
