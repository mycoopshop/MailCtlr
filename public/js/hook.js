$(document).on('keydown','form',function(e,t){
	if (e.keyCode === 13) {
		var i = $(this).find('[type=text],select');
		i.eq(i.index(e.target)+1).focus();
		e.preventDefault();
		return false;		
	}
});