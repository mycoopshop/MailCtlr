<?php

function checkbox_value($value) {
	$o = $value ? 'value="1" checked="checked"' : 'value="1"';	
	return $o;
}

function radio_value() {
	
	
}

function select_option_value($value,$selected) {
	$o = ' value="'.$value.'" ';
	if ($value==$selected) {
		$o.= ' selected="selected" ';
	}
	return $o;		
}

function text_value() {
	
	
	
}