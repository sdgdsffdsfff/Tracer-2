$(function() {
	$('#tabs' ).tabs({ 
		cache: true,
		spinner: 'Preparing data...'
	});
	$('input:submit').button();
});
