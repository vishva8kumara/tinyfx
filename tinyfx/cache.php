<?php

function cache_page($data, $module, $method, $params){
	$filename = "data/cache/".sha1($module."/".$method."/".rip_array($params));
	//echo $filename;
	$handle = fopen($filename, "a");
	fwrite($handle, $data);
	fclose($handle);
}

function rip_array($arr){
	$tmp = "";
	foreach ($arr as $key => $val)
		if (is_array($val))
			$tmp .= $key.":".rip_array($val).";";
		else
			$tmp .= $key.":".$val.";";
	return $tmp;
}

?>
