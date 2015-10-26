(function($){
	
	var Spacejs = {
		
		rxStack:[],
		
		tx:function(clientid,channel,data) {
			
			console.log("TX:",clientid,channel,data);
			
			Spacejs.ws("tx.php",{
				clientid:clientid,
				channel:channel,
				data:Spacejs.serialize(data)
			},function(resp){
				console.log("TR:",resp);
			});	
			
		},
	
		rx:function(clientid,channel,callback) {
			Spacejs.rxStack.push({
				clientid:clientid,
				channel:channel,
				callback:callback
			});			
		},
		
		ws:function(endpoint,data,callback) {
			$.ajax({
				url:"http://www.javanile.org/develop/spacejs/ws/"+endpoint,
				type:"POST",
				data:data,
				dataType:"JSON",
				async:true,
				success:function(resp) {
					callback(resp)
				},
				complete:function(resp) {
					
				},
				error:function(resp) {
					console.log('error',endpoint,resp);
				}
			});						
		},
		
		serialize:function(data) {
			var s = JSON.stringify(data);
			return s;			
		},
		
		polling:function(){
			try {
				console.log("listen:",Spacejs.rxStack);
				Spacejs.ws("rx.php",{
					listen:Spacejs.rxStack
				},function(o){
					
				});
			} catch (e) {
				
			}			
			setTimeout("Spacejs.polling();",5000);
		}
		
	};
	
	window.Spacejs = Spacejs;
	
	$(document).ready(function(){
		setTimeout("Spacejs.polling();",1);
	});
	
})(jQuery);



