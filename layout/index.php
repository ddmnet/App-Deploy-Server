<!DOCTYPE html>
<html>
	<head>
		<title>DDM App Deployment Panel</title>
		<link href="3p/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
		<link href="3p/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen" />
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="/">DDM Apps</a>
				</div>
			</div>
		</div>
		<div class="container">
		<h1>DDM App Deployment Portal</h1>
<?
		$chunks = array_chunk($apps, 3);
		foreach ($chunks as $row) :
?>
			<div class="row">
<?
				foreach ($row as $app):
					$install_url = $app->url . $app->name . '.plist';
?>
				<div class="span4">
					<img src="<?=$app->get_icon_url()?>" height="72" width="72" />
					<h4><?=$app->get_title()?></h4>
					<p>
						<a class="btn btn-primary btn-large" href="itms-services://?action=download-manifest&url=<?=$install_url?>">Install</a>
					</p>
				</div>
<?
				endforeach;
?>
			</div>
<?			
		endforeach;
?>
		</div>
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<script src="/3p/bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>