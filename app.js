
jQuery(function($) {

	$('a.activate-feed').on('click', function(e) {
		e.preventDefault();

		var $this = $(this),
			url = $this.attr('href'),
			name = prompt('Blog name..?', '');

		if (name) {
			location = url + name;
		}
	});

});
