<?php

	function render_view($view, $data){
		global $base_url, $base_url_public, $public_files_root, $user, $module, $method;
		// Turn $data array to variables
		if (isset($data))
			foreach ($data as $key => $val)
				$$key = $val;
		// Render view to the output_buffer
		ob_start();
			if (file_exists("views/".$view.".php"))
				include "views/".$view.".php";
			else
				require_once "templates/error_404.php";
			$yield = ob_get_contents();
		ob_end_clean();
		return $yield;
	}

	function render_template($template, $yield, $html_head = false){
		global $base_url, $base_url_public, $user, $module, $method; //, $html_head;
		// Render the view inside the template to the output_buffer
		if ($html_head == false){
			$html_head = array('title' => ucfirst($module)." ".ucfirst(str_replace("_", " ", $method)));
		}
		ob_start();
			if (file_exists("templates/".$template)){
				include "templates/".$template;
				$yield = ob_get_contents();
			}
			else{
				require_once "templates/error_404.php";
			}
		ob_end_clean();
		//
		return $yield;
	}

	/*$html_head = "";
	function HTML_head_begin(){
		ob_start();
	}
	function HTML_head_end(){
		global $html_head;
		$html_head = ob_get_contents();
		ob_end_clean();
	}*/

	function render_table($data, $schema, $row_extra = ""){
		global $base_url;
		if (!is_array($schema)){
			report_error("Render Error", "Data schema is invalid");
			handle_error(0, "Data schema is invalid", "Render Error", "Render Error", $data);
			return false;
		}
		ob_start();
		?><table class="data_table" border="0" cellpadding="0" cellspacing="0" width="100%"><tr><?php
		foreach ($schema as $col_name => $col_attribs){
			?><th><?= $col_attribs[0]; ?></th><?php
		}
		?></tr><?php
		$alt_row = false;
		while ($row = mysql_fetch_array($data, MYSQL_ASSOC)){
			?><tr <?= ($alt_row)?"class=\"alt_row\"":""; ?> <?= str_replace("{id}", $row["id"], $row_extra); ?>><?php
			foreach ($schema as $col_name => $col_attribs){
				if ($col_attribs[1] == "link"){
					?><td align="center"><a href="<?= $base_url; ?><?= str_replace("{id}", $row["id"], $col_attribs[2]); ?>"<?= isset($col_attribs[3])?" onclick=\"".$col_attribs[3]."\"":""; ?>><?= $col_attribs[0]; ?></a></td><?php
				}
				else if ($col_attribs[1] == "url"){
					?><td align="center"><a href="<?= $row[$col_name]; ?>" target="_blank">Link</a></td><?php
				}
				else if ($col_attribs[1] == "img"){
					?><td align="center"><img src="<?= $col_attribs[2].$row[$col_name]; ?>" width="<?= isset($col_attribs[3]) ? $col_attribs[3] : '50'; ?>"></td><?php
				}
				else if ($col_attribs[1] == "date-time"){
					?><td align="center"><?= beautify_datetime($row[$col_name]); ?></td><?php
				}
				else if ($col_attribs[1] == "function"){
					?><td align="center"><?= $col_attribs[2]($row[$col_name]); ?></td><?php
				}
				else{
					?><td <?= ($col_attribs[1] == "num")?"align=\"right\"":""; ?>><?= $row[$col_name]; ?>&nbsp;</td><?php
				}
			}
			?></tr><?php
			$alt_row = !$alt_row;
		}
		?></table><?php
		$yield = ob_get_contents();
		ob_end_clean();
		//
		return $yield;
	}

	function checkbox_checked($val){
		if ($val === true || $val == 1 || $val == "1")
			return "checked=\"true\"";
		else
			return "";
	}

	function flash_message($message, $level, $fadeout = false){
		if (!isset($_SESSION["flash_messages"]))
			$_SESSION["flash_messages"] = array();
		$_SESSION["flash_messages"][] = array($level, $message, $fadeout);
	}

	function flash_message_dump(){
		if (!isset($_SESSION["flash_messages"]) || count($_SESSION["flash_messages"]) == 0)
			return true;
		//
		echo '<div id="flash_messages">';
		//if (count($_SESSION["flash_messages"]) > 1)
		//	echo '<a href="javascript:popup_bring_down(\'flash_messages\', 100);">dismiss all</a>';
		foreach ($_SESSION["flash_messages"] as $flash_message){
			$div_id = rand(20, 34956344).'_'.time();
			echo '<div class="'.$flash_message[0].'" id="flash_message_'.$div_id.'"><div class="icon"></div>'.$flash_message[1].
				'<a class="dismiss" href="javascript:popup_bring_down(\'flash_message_'.$div_id.'\', 100);">dismiss</a></div>';
			if ($flash_message[2])
				echo '<script>setTimeout("popup_bring_down(\'flash_message_'.$div_id.'\', 100);", 1600);</script>';
		}
		echo '</div>';	//'<script>setTimeout("popup_bring_down(\'flash_messages\', 100);", '.(count($_SESSION["flash_messages"])*4800).');</script>'.
		//
		$_SESSION["flash_messages"] = array();
	}

	function shorten_string($string, $len, $content_id = false, $skip = 0){
		global $base_url;
		/*while (true){
			$p0 = stripos($string, "<script");
			if ($p0 === false)
				break;
			//$tag = substr($string, $p0 + 1, strpos($string, array(">", " "), $p0));
			$p2 = stripos($string, "</script>", $p1);
			echo "{".$p0.":".$p2."}";
			$string = substr($string, 0, $p0).substr($string, $p2 + 9);
		}*/
		$string = str_replace("\t", '', $string);
		$string = str_replace(array("\n", "\r", '&nbsp;', '  '), ' ', $string);
		$string = preg_replace('#<script(.*?)</script>#s', '', $string);
		$string = str_replace(array('&', '<', '>', '"'), array('&amp;', '&lt;', '&gt;', '&quot;'), $string);
		//
		$len += $skip;
		if (strlen($string) < $len)
			return substr($string,  $skip);
		$tmp = strpos($string, " ", $len);
		if ($tmp === false)
			return substr($string,  $skip);
		$shorten_count = uniqid();
		//$string = $string;
		if ($content_id == false)
			return substr($string, $skip, $tmp - $skip);
		else
			return substr($string, $skip, $tmp - $skip)." <a href=\"".$base_url."content/view/".$content_id."\" onclick=\"show_more_text('".$shorten_count."', '".$content_id.":x', ".$tmp."); return false;\" id=\"shorten_more_link_".$shorten_count."\">...more</a>".
				"<span style=\"display:none;\" id=\"shorten_more_".$shorten_count."\">"./*substr($string, $tmp, $tmp).*/"</span>";
	}

	function beautify_datetime($datetime){
		global $lang;
//print_r($lang);
//echo $lang['interface']['am'];
		$time = is_numeric($datetime) ? $datetime : strtotime($datetime);
		$now = time();
		$diff = $now - $time;
		if ($time == 0)
			return $lang['interface']['never'];
		if ($diff < 86400){
			if ($diff < 0)
				return _beautify_datetime_future($datetime);
			else if ($diff < 5)
				return $lang['interface']['just_now'];
			else if ($diff < 60)
				return $lang['interface']['seconds'].$diff.$lang['interface']['seconds_ago'];
			else{
				$diff = floor($diff / 60);
				if ($diff < 60)
					return $lang['interface']['minutes'].$diff.$lang['interface']['minutes_ago'];
				else if (date("j", $now) == date("j", $time))
					/*if ($diff < 480)
						return minutes_to_hours($diff)." hours ago";
					else*/
					return $lang['interface'][date('a', $time).'_pre'].date("g:i", $time).$lang['interface'][date('a', $time)];
				else
					return $lang['interface']['yesterday'].' '.$lang['interface'][date('a', $time).'_pre'].date("g", $time).' '.$lang['interface'][date('a', $time)];
			}
		}
		else
			if (date("Y", $now) == date("Y", $time))
				if (date("n", $now) == date("n", $time))
					if (date("W", $now) == date("W", $time))
						return $lang['interface'][date('l', $time)].' '.$lang['interface'][date('a', $time).'_pre'].date("g", $time).' '.$lang['interface'][date('a', $time)];
					else
						return date("j", $time).$lang['interface'][date("S", $time)].' '.$lang['interface'][date("M", $time)].' '.$lang['interface'][date('a', $time).'_pre'].date("g", $time).' '.$lang['interface'][date('a', $time)];
				else
					return date("j", $time).$lang['interface'][date("S", $time)].' '.$lang['interface'][date("M", $time)];
			else
				return $lang['interface'][date("M", $time)].' '.date("Y", $time);
	}
	function _beautify_datetime_future($datetime){
		$time = strtotime($datetime);
		$now = time();
		$diff = $time - $now;
		if ($diff < 86400){
			if ($diff < 5)
				return "Just now";
			else if ($diff < 60)
				return "in ".$diff." seconds";
			else{
				$diff = floor($diff / 60);
				if ($diff < 60)
					return "in ".$diff." minutes";
				else if (date("j", $now) == date("j", $time))
					/*if ($diff < 480)
						return minutes_to_hours($diff)." hours ago";
					else*/
					return date("g:i a", $time);
				else
					return "Tomorrow ".date("g a", $time);
			}
		}
		else
			if (date("Y", $now) == date("Y", $time))
				if (date("n", $now) == date("n", $time))
					if (date("W", $now) == date("W", $time))
						return date("l g a", $time);
					else
						return date("jS M g a", $time);
				else
					return date("jS M", $time);
			else
				return date("M Y", $time);
	}
	/*function minutes_to_hours($minutes){
		$hours = floor($minutes / 60);
		$minutes = $minutes - ($hours * 60);
		return $hours.":".str_pad($minutes, 2, "0", STR_PAD_LEFT);
	}*/
	function slugify($text){
		//$text = preg_replace('/[^a-zA-Z0-9\/_|+ -]/', '-', $text);
		$text = preg_replace('/[\/_|+ -.]+/', '-', $text);
		$text = trim($text, '-');
		$text = strtolower($text);
		return $text;
	}

?>
