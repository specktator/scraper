<?php

$a = [['a'=>1, 'b'=>'1', 'c'=>'asd'],['a'=>1, 'b'=>'1', 'c'=>'asd'],null];
// var_dump(empty($a[0]));
var_dump(array_filter($a,function($value){
	return (empty($value))? FALSE : TRUE ;
}));
?>