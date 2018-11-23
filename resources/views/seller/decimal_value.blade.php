<?php
function amount_with_decimal($amt){
	$decimal_places = 2;
	  	$decimal_val = explode('.', $amt);			  
	   if(isset($decimal_val[1])){
			$decimal = rtrim($decimal_val[1],0);
			if(strlen($decimal)>2)
			$decimal_places = strlen($decimal);
			if($decimal_places > 8)
				$decimal_places = 8;
		}
		echo number_format($amt,$decimal_places,'.',',');
}
function amount_with_decimal2($amt){
	$decimal_places = 2;
	  	$decimal_val = explode('.', $amt);			  
	   if(isset($decimal_val[1])){
			$decimal = rtrim($decimal_val[1],0);
			if(strlen($decimal)>2)
			$decimal_places = strlen($decimal);
			if($decimal_places > 8)
				$decimal_places = 8;
		}
		return number_format($amt,$decimal_places,'.',',');
}
function amount_with_decimal_withoutcomma($amt){
	$decimal_places = 2;
	  	$decimal_val = explode('.', $amt);			  
	   if(isset($decimal_val[1])){
			$decimal = rtrim($decimal_val[1],0);
			if(strlen($decimal)>2)
			$decimal_places = strlen($decimal);
			if($decimal_places > 8)
				$decimal_places = 8;
		}
		return number_format($amt,$decimal_places,'.','');
}
?>