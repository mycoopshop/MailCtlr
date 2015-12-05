$ui.uploadifyImport = function(obj) {
	var f = jQuery(obj);
	var s = f.parents("[data-ui-uploadify]").first();
	var c = $ui.getConfig(s,"data-ui-uploadify");			
	var i = f.attr("data-ui-uploadify-import");
	c.viewer = $ui.urlParamsUpdate(c.service,{
		id:i,
		action:'Mostra'
	});	
	alert('IMPORTO!!YEAHHH');
	window.open(c.viewer,'_blank'); 
};

jQuery(document).on("click","[data-ui-uploadify-import]",function(e){	
	$ui.uploadifyImport(this);	
	e.preventDefault();
	return false;
});
