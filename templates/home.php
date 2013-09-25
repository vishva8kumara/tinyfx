<!DOCTYPE html>
<html>
	<head>
		<title><?= $html_head["title"]; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="shortcut icon" href="<?= $base_url_public; ?>fav.ico" />
		<link rel="stylesheet" href="<?= $base_url_public; ?>css/style.css" media="all" />
		<script type="text/javascript" src="<?= $base_url_public; ?>js/ajax.js"></script>
	</head>
	<body>
		<div class="wrapper_outer">
			<div class="hwrapper">
				<a href="<?= $base_url; ?>"><h1>Site Title</h1></a>
			</div>
			<div class="wrapper">
				<ul class="nav">
					<li <?= $module=='index' && $method=='index' ? 'class="current"' : ''; ?>><a href="<?= $base_url; ?>">Home</a></li>
				</ul>
				<?php flash_message_dump(); ?>
				<?= $yield; ?>
			</div>
		</div>
		<div id="footer">
			Footer
		</div>
	</body>
</html>