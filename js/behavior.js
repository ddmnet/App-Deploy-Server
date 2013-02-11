$('#infoModal').on('shown', function() {
	$(".modal-footer a.install").attr('href', $('.modal-body a.applink').attr('href'));
});

if (window.navigator.standalone) {
	var local = document.domain;
	$('a').click(function() {
		var a = $(this).attr('href');
		if ( a.match('http://' + local) || a.match('http://www.' + local) ){
			event.preventDefault();
			document.location.href = a;
		}
	});
}