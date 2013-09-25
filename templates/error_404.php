<?php
		ob_start();
?>
<h1>404 - File not Found</h1>
<?php
		$yield = ob_get_contents();
		ob_end_clean();
		include 'templates/home.php';
?>