<?php

function array_to_xml($namespace, $array){
	return "\n<".$namespace.">\n".array2xml($array, 1)."</".$namespace.">\n\n";
}

function xml_to_array($namespace, $xml_str){
	$p1 = strpos($xml_str, "<".$namespace.">") + strlen($namespace) + 2;
	$p2 = strpos($xml_str, "</".$namespace.">");
	return xml2array(substr($xml_str, $p1, $p2 - $p1));
}

function xml2array($xml_str){
	$tmp_array = array();
	$tag = "";
	$p2 = 0;
	$i = 0;
	$cou = 1;
	$length = strlen($xml_str);
	while ($i < $length){
		$i = strpos($xml_str, "<", $i);
		if ($i === false)
			break;
		$i = $i + 1;
		$p2 = strpos($xml_str, ">", $i);
		$tag = substr($xml_str, $i, $p2 - $i);
		//
		$i = $p2 + 1;
		$p2 = strpos($xml_str, "</".$tag.">", $i);
		$data = substr($xml_str, $i, $p2 - $i);
		//
		$i = $p2 + 3;
		if (substr($tag, 0, 5) == "elem_" && is_numeric(substr($tag, 5))){
			$tag = substr($tag, 5);
		}
		if (isset($tmp_array[$tag])){
			$cou += 1;
			$tag = $tag."_".$cou;
		}
		if (strpos($data, "<") === false){
			$tmp_array[$tag] = html_entity_decode($data);
//echo "<".$tag.">";
//echo $data."\n";
		}
		else{
			$tmp_array[$tag] = xml2array($data);
//echo "<".$tag.">";
		}
		if ($i >= $length)
			break;
	}
//print_r($tmp_array);
	return $tmp_array;
}

function array2xml($array, $indent){
	$buffer = "";
	$i = 0;
	foreach ($array as $key => $value){
		if (is_resource($value)){
			$rec_id = 1;
			$buffer .= str_repeat("\t", $indent)."<".$key.">\n";
			while ($row = mysql_fetch_array($value)){
				$buffer.= str_repeat("\t", $indent)."	<record_".$rec_id.">\n";
				foreach ($row as $col_name => $val)
					if (!is_numeric($col_name))
						$buffer.= str_repeat("\t", $indent)."		<".$col_name.">".htmlentities($val)."</".$col_name.">\n";
				$buffer.= str_repeat("\t", $indent)."	</record_".$rec_id.">\n";
				$rec_id += 1;
			}
			$buffer.= str_repeat("\t", $indent)."</".$key.">\n";
		}
		else if (is_array($value)){
			$buffer .= str_repeat("\t", $indent)."<".$key.">\n".
					array2xml($value, $indent+1).
					str_repeat("\t", $indent)."</".$key.">\n";
		}
		else if(is_numeric($key)){
			$buffer .= str_repeat("\t", $indent)."<elem_".$key.">".($value)."</elem_".$key.">\n";
		}
		else{
			$buffer .= str_repeat("\t", $indent)."<".$key.">".htmlentities($value)."</".$key.">\n";
		}
		$i++;
	}
	return $buffer;
}

?>
