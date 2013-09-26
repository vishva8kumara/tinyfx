<?php
ini_set('session.cookie_domain', $domain);
session_start();

$db_connection = false;
if (isset($_SESSION["user"]))
	$user = $_SESSION["user"];

$query_string = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

if (substr($query_string, -7) == 'PIE.htc')
	die(file_get_contents('PIE.htc'));

$p1 = strpos($query_string, $domain);
if ($p1 !== false){
	$p2 = strrpos($query_string, "/", $p1 - strlen($query_string));
	if ($p2 + 1 < $p1){
		$subdomain = substr($query_string, $p2 + 1, $p1 - $p2 - 2);
		$base_url = 'http://'.$subdomain.'.'.$domain.'/';
	}
	$query_string = substr($query_string, $p1+strlen($domain));
}
if (substr($query_string, 0, 1) == "/")
	$query_string = substr($query_string, 1);

$params = explode('/', $query_string);
$params_count = count($params);

// Determine the Module
$module = array_shift($params);
if ($module == '')
	$module = 'index';

// Determine the Method
if ($params_count < 2)
	$method = 'index';
else
	$method = array_shift($params);
if ($method == '')
	$method = 'index';

// Append GET, POST to params
foreach ($_POST as $key => $val)
	$params[$key] = $val;

		require_once 'render.php';
		// Include the Module
		if (file_exists('modules/'.$module.'.php'))
			require_once 'modules/'.$module.'.php';
		else
			require_once 'templates/error_404.php';

		// Call the Method
		if (function_exists($method)){
			$data = $method($params);
			if (isset($params['format']) && $params['format'] == 'xml'){
				header('Content-type: text/xml');
				require_once 'xmlarray.php';
				$yield = array_to_xml($module.'_'.$method, array('parameters' => $params, 'output' => $data));
			}
			else{
				$yield = render_view($module."/".$method, $data);
				if (isset($template_file) && $template_file != "" && (!isset($params['format']) || $params['format'] != 'js'))
					$yield = render_template($template_file, $yield, (isset($data['html_head']) ? $data['html_head'] : false));//["yield"], $yield["head"]);
			}
			if ($minify_html){
				$yield = str_replace("\t", '', $yield);
				$yield = str_replace(array("\n", "\r", '   ', '  '), ' ', $yield);
				$yield = preg_replace("/<!--.*-->/Uis", "", $yield);
			}
			if ($compress_html){
				global $HTTP_ACCEPT_ENCODING;
				if (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false)
					$encoding = 'x-gzip';
				else if (strpos($HTTP_ACCEPT_ENCODING, 'gzip') !== false)
					$encoding = 'gzip';
				else
					$encoding = false;
				if ($encoding){
					$size = strlen($file);
					if ($size > 2048){
						header('Content-Encoding: '.$encoding);
						print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
						$file = gzcompress($file, 9);
						$file = substr($file, 0, $size);
					}
				}
			}
			echo $yield;
			if (function_exists('cache_page'))
				cache_page($yield, $module, $method, $params);
		}
		else{
			require_once 'templates/error_404.php';
		}

	if ($db_connection != false){
		$db_connection->close();
	}

function connect_database(){
	global $db_connection;
	if ($db_connection == false){
		include 'interfaces/database.php';
		$db_connection = new MySQL;
	}
	return $db_connection;
}

function redirect($module, $method = false, $params = false, $redirect_after = false){
	global $base_url;
	if ($redirect_after != false)
		$_SESSION['REDIRECT_AFTER_SIGNIN'] = $redirect_after;
	$params_list = "";
	if ($params)
		foreach($params as $par)
			$params_list .= '/'.$par;
	if(!$method)
		$method = 'index';
	header('location:'.$base_url.$module.'/'.$method.$params_list);
	die();
}

?>
