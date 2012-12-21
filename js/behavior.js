$('#infoModal').on('shown', function() {
	$(".modal-footer a.install").attr('href', $('.modal-body a.applink').attr('href'));
});