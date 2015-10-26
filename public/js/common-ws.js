;(function($){
	
	// common 
	var $ws = {
		
		base:"",
		
		req:function(endpoint,params,callback) {
			
			var async = true;
			var ret = null;			
			
			if (typeof callback === 'undefined') {
				async = false;
			} 
			
			$.ajax({
				url: $ws.base+"/"+endpoint,	
				type: "POST",
				data: params,
				dataType: "JSON",
				cache: false, 
				contentType: "application/x-www-form-urlencoded",
				async: async,
				success:function(resp) {
					if (async) { 
						callback(resp);
					} else {
						ret = resp;					
					}					
				},
				complete:function(resp) {					
				},
				error:function(resp) {
					console.log("Error ($ws.req "+endpoint+"):",resp);
				}
			});	
			return ret;
		}
	};
			
	// declare global
	window.$ws = $ws;
	
}(jQuery));



