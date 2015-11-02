
var isValidDate = function (value, userFormat) 
{
  var 
  uF = userFormat || 'dd/mm/yyyy', // default format
  
  delimiter = /[^mdy]/.exec(uF)[0],
  theFormat = uF.split(delimiter),
  theDate = value.split(delimiter),

  isDate = function (date, format) 
  {
    var m, d, y;
    for (var i = 0, len = format.length; i < len; i++) {
      if (/m/.test(format[i])) m = date[i];
      if (/d/.test(format[i])) d = date[i];
      if (/y/.test(format[i])) y = date[i];
    }
    return (
      m > 0 && m < 13 &&
      y && y.length === 4 &&
      d > 0 && d <= (new Date(y, m, 0)).getDate()
    );
  };

  return isDate(theDate, theFormat);
};
// chech for int value from -9007199254740990 to 9007199254740990
function isInt(n)
{
    return +n === n && !(n % 1);
}
//  Any number including Infinity and -Infinity but not NaN
function isFloat(n)
{
    return +n === n;
}
function nl2br (str, is_xhtml) 
{
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Philip Peterson
    // +   improved by: Onno Marsman
    // +   improved by: Atli ��r
    // +   bugfixed by: Onno Marsman
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Maximusya
    // *     example 1: nl2br('Kevin\nvan\nZonneveld');
    // *     returns 1: 'Kevin<br />\nvan<br />\nZonneveld'
    // *     example 2: nl2br("\nOne\nTwo\n\nThree\n", false);
    // *     returns 2: '<br>\nOne<br>\nTwo<br>\n<br>\nThree<br>\n'
    // *     example 3: nl2br("\nOne\nTwo\n\nThree\n", true);
    // *     returns 3: '<br />\nOne<br />\nTwo<br />\n<br />\nThree<br />\n'
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';

    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
function make_form_readonly_action(selector) {
	var form = jQuery(selector);
	jQuery(":input",form).each(function(){
		var item = jQuery(this);
		if (!item.is("input[type=hidden],button")) {
			if (item.is("select")) {
				var ival = $("option:selected",item).text();
			} else {
				var ival = item.val();
			}			
			var name = item.attr("name");
                        var classe = item.attr("class");
			var span = jQuery('<span/>');
			span.html(ival);
			//span.addClass("form-control");
                        span.attr('name',name);
                        span.attr('class',classe);
			item.before(span);
			item.remove();
		} else {
                        /*if (item.is('[data-ui-render]')){
                            var render = item.attr('data-ui-render');
                            item.removeAttr('data-ui-render');
                            var n = render.replace(/input/gi,'span');
                            item.attr('data-ui-render',n);
                        }*/
                        /*
                        if (typeof render !== typeof undefined && render !== false) {
                            //console.log(render);
                            item.removeAttr('data-ui-render');
                            var n = render.replace('input','span');
                            console.log(n);
                           // item.attr('data-ui-render',n);
                        }*/
			item.hide();
		}
	});
	
	// hide uploadify controls
	jQuery("[data-ui-uploadify-delete]",form).hide();
	jQuery(".uploadify",form).hide();
	
	//hide input group btn
	jQuery("div .input-group",form).each(function(){
		var item = jQuery(this);
		item.removeAttr("class");
                item.find("span .form-control").attr("class","display:none;");
	});        
	jQuery(".no-readonly",form).hide();
	jQuery(selector).show();
}
function make_form_readonly(selector) {
	jQuery(selector).hide();
	setTimeout('make_form_readonly_action("'+selector+'");','200'); //5secondi	
}
jQuery(document).on("keyup",".fill-with-16-chars",function(e){	
	if(e.keyCode===220) {
		function makeid() {
			var text = "";
			var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

			for( var i=0; i < 16; i++ )
				text += possible.charAt(Math.floor(Math.random() * possible.length));

			return text;
		}
		var s = makeid();
		$(this).val(s);
	}
});
$ui.testing.min3=function(data){
    var valore=jQuery(data).val();
    if (valore.length >= 3 ){
        return true;
    }
    return false;
};
$ui.testing.min2=function(data){
    var valore=jQuery(data).val();
    if (valore.length >= 2 ){
        return true;
    }
    return false;
};
function submitForm() {
	console.log("form#item submit()");
	jQuery("form#item").submit();
}
$ui.testing.cap_check=function(data){
    var valore=jQuery(data).val();
    if (valore.length == 5 ){
        return true;
    }
    return false;
};
$ui.testing.maggiore0=function(data){
    var valore=jQuery(data).val();
    if (valore > 0 ){
        return true;
    }
    return false;
};