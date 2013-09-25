<?php	// Vishva Kumara | vishva8kumara@gmail.com

$find = array('&lt;', '&gt;', '&amp;', '&quot;', '&lsquo;', '&rsquo;', '&#39;', '%3D', '%26', '&apos;', 'â€œ', 'â€');
$replace = array('<', '>', '&', '"', "'", "'", "'", '=', '&', "'", '"', '"');

$removed_date_part = '';

function rss_to_array($rss_data, $filter = false){
	global $removed_date_part;
	$tmp_array = array();
	$tag = 'item';
	$tmp_str = '';
	$i = 0;
	$j = 0;
	while (true){
		$i = strpos($rss_data, '<'.$tag.'>', $i);
		$j = strpos($rss_data, '</'.$tag.'>', $i);
		if ($i === false || $j === false)
			break;
		$i = $i+strlen($tag)+2;
		$tmp_str = substr($rss_data, $i, $j-$i);
		$x_array = array();
		//$x_array['raw'] = $tmp_str;
		$x_array['title'] = normalize_string(tag_data_from_xml($tmp_str, 'title'));
		$x_array['pubDate'] = tag_data_from_xml($tmp_str, 'pubDate');
		$x_array['pubDate'] = strtotime($x_array['pubDate']);
		if ($filter == 'Google'){
			$x_array['link'] = google_url_filter(tag_data_from_xml($tmp_str, 'link'));
			$x_array['description'] = google_description_filter(tag_data_from_xml($tmp_str, 'description'));
		}
		else{
			$x_array['link'] = normalize_string(tag_data_from_xml($tmp_str, 'link'));
			$tmp_description = tag_data_from_xml($tmp_str, 'content:encoded');
			$tmp_description_2 = tag_data_from_xml($tmp_str, 'atom:summary');
			if ($tmp_description == false || $tmp_description == '')
				$x_array['description'] = normalize_string(tag_data_from_xml($tmp_str, 'description'));
			else
				$x_array['description'] = normalize_string($tmp_description);
			if ($x_array['description'] == false || $x_array['description'] == '')
				$x_array['description'] = normalize_string($tmp_description_2);
		}
		$x_array['description'] = str_replace(array('&lt;', '&gt;'), array('<', '>'), $x_array['description']);
		//
		$p0 = strpos($x_array['description'], '<img');
		$p2 = strpos($tmp_str, 'media:thumbnail');
		if ($p0 !== false){
			$p0 = strpos($x_array['description'], 'src', $p0);
			$p0 = strpos($x_array['description'], '"', $p0)+1;
			$p1 = strpos($x_array['description'], '"', $p0+4);
			$x_array['image'] = substr($x_array['description'], $p0, $p1-$p0);
		}
		else if ($p2 !== false){
			$p0 = strpos($tmp_str, 'url', $p2);
			$p0 = strpos($tmp_str, '\'', $p0)+1;
			$p1 = strpos($tmp_str, '\'', $p0+4);
			$x_array['image'] = substr($tmp_str, $p0, $p1-$p0);
		}
		else{
			$x_array['image'] = '';
		}
		//
		$x_array['description'] = html_entity_decode(html_entity_decode(strip_tags($x_array['description'])));
		$x_array['domain'] = domain_from_url($x_array['link']);
		if ($x_array['pubDate'] == '')
			$x_array['pubDate'] = strtotime($removed_date_part);
		array_push($tmp_array, $x_array);
	}
	return $tmp_array;
}

function tag_data_from_xml($item_data, $tag){
	$i = strpos($item_data, '<'.$tag);
	$j = strpos($item_data, '</'.$tag.'>', $i);
	if (!($i === false || $j === false)){
		$i = strpos($item_data, '>', $i)+1;
		//$i = $i+strlen($tag)+2;
		$tmp_str = substr($item_data, $i, $j-$i);
		return $tmp_str;
	}
	return '';
}

function normalize_string($str){
	global $find, $replace, $removed_date_part;
	$str = trim($str);
	if (substr($str, 0, 9) == '<![CDATA['){
		$output = substr($str, 9, strlen($str) - 12);
	}
	else{
		$output = $str;
	}
	$output = /*strip_tags*/(str_replace($find, $replace, $output));
	if (substr($output, 0, 14) == ' (Lanka-e-News'){
		$split_point = strrpos($output, ')')+1;
		$removed_date_part = substr($output, 16, $split_point-17);
		$output = substr($output, $split_point);
	}
	else{
		$removed_date_part = '';
	}
	return $output;
}

function domain_from_url($url){
	$i = strpos($url, '//');
	if (strlen($url) < $i+3)
		return $url;
	$j = strpos($url, '/', $i+3);
	if (!($i === false || $j === false)){
		$i = $i+2;
		return substr($url, $i, $j-$i);
	}
	return $url;
}

function google_description_filter($item_data){
	global $find, $replace;
	$data = str_replace($find, $replace, $item_data);
	$i = strpos($data, '</font></b></font><br /><font size="-1">');
	$j = strpos($data, '</font>', $i+40);
	if (!($i === false || $j === false)){
		$i = $i+40;
		return strip_tags(substr($data, $i, $j-$i));
	}
	return '';
}

function google_url_filter($item_data){
	global $find, $replace;
	$i = strpos($item_data, 'url=');
	if (!($i === false))
		return str_replace($find, $replace, substr($item_data, $i+4));
	return $item_data;
}

?>
