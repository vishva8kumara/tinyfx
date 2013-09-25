<?php

	function unzip_archive($filename, $destination){
		//echo $filename.":".$destination;
		$in_file = substr(__FILE__, 0, strrpos(__FILE__, "\\"));
		$in_file = str_replace("\\", "/", $in_file);
		$in_file .= "/../".$filename;
		//$in_file .= $filename;

		/*if (!file_exists($in_file)){
			$in_file = "./".$filename;
			if (!file_exists($in_file))
				die('File does not exist');
		}*/

		$z = zip_open($in_file);
		if (is_numeric($z))
			die("$z: Can't open $in_file!");

		while ($entry = zip_read($z)){
			$entry_name = zip_entry_name($entry);
			//echo $entry_name.":".zip_entry_filesize($entry)."<br/>";
			if (zip_entry_filesize($entry)){
				$dir = $destination."/".dirname($entry_name);
				//echo $dir."<br/>";
				if (!is_dir($dir))
					mkdir($dir, 0777, true);
				$file = basename($entry_name);
				if (zip_entry_open($z, $entry)){
					if ($fh = fopen($dir.'/'.$file, 'w')){
						fwrite($fh, zip_entry_read($entry, zip_entry_filesize($entry)))
							or die("Can't write file!");//error_log("can't write: $php_errormsg");
						fclose($fh) or die("Can't close file!");//error_log("can't close: $php_errormsg");
					}
					else{
						die("Can't open $dir/$file!");
						//error_log("can't open $dir/$file: $php_errormsg");
					}
					zip_entry_close($entry);
				}
				else{
					die("Can't open entry $entry_name!");
					//error_log("can't open entry $entry_name: $php_errormsg");
				}
			}
		}
	}

	$files_list = false;
	function list_folder($root){
		global $files_list;
		if (is_dir($root)) {
			if ($dh = opendir($root)) {
				while (($file = readdir($dh)) !== false) {
					if ($file == "." || $file == ".."){
					}
					else if (is_dir($root."/".$file)){
						list_folder($root."/".$file);
					}
					else {
						$files_list[] = $root."/".$file;
						//echo $root."/".$file."<br/>";
					}
				}
				closedir($dh);
			}
		}
	}

	function compress_folder($path, $destination){
		global $files_list;
		$files_list = array();
		list_folder($path);
		//print_r($files_list);
		include "interfaces/zipfile.inc.php";
		$zipfile = new zipfile();
		foreach($files_list as $file){
			$filedata = file_get_contents($root.$file);
			$zipfile->add_file($filedata, substr($file, strlen($path)+1));
			//echo $file;
			//echo $root.$file.":".substr($file, strlen($path))."<br/>";
		}
		//return false;
		$handle = fopen($destination, "w");
		fwrite($handle, $zipfile->file());
		fclose($handle);
		if (!file_exists($destination)){
			die('Compression failure!');
		}
		else{
			return true;
			//echo'Folder compressed.\n';
		}
	}

?>
