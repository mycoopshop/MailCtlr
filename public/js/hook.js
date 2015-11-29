$(document).on('keydown','form',function(e,t){
	if (e.keyCode === 13) {
		var i = $(this).find('[type=text],select');
		i.eq(i.index(e.target)+1).focus();
		e.preventDefault();
		return false;		
	}
});

(function($){
	$(document).ready(function(){
		$('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
			event.preventDefault(); 
			event.stopPropagation(); 
			$(this).parent().siblings().removeClass('open');
			$(this).parent().toggleClass('open');
		});
	});
})(jQuery);