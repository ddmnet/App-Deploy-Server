<!DOCTYPE html>
<html>
	<head>
		<title>DDM App Deployment Panel</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/layout/img/ddm-icon-72x72.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/layout/img/ddm-icon-114x114.png">
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/layout/img/ddm-icon-144x144.png">
		<link rel="apple-touch-icon-precomposed" href="/layout/img/ddm-icon-57x57.png">
		<link href="3p/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
		<link href="3p/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen" />
		<link href="layout/css/style.css" rel="stylesheet" media="screen" />
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="/">DDM App Deployment Portal</a>
				</div>
			</div>
		</div>
		<div id="primary" class="container">
<?
		$chunks = array_chunk($apps, 3);
		foreach ($chunks as $row) :
?>
			<div class="row">
<?
				foreach ($row as $app):
					$install_url = $app->url . $app->name . '.plist';
					$itms_url = "itms-services://?action=download-manifest&url=$install_url";
					$icon = $app->get_icon_url(false);
					if (file_exists($icon)) {
						$img_src = 'src="' . $icon . '"';
					} else {
						$img_src = 'data-src="holder.js/72x72/social"';
					}

					$version = $app->get_metadata('bundle-version');
					$versionString = '<small>v. ' . $version . '</small>';

					$subtitle = $app->get_metadata('subtitle');
					if ($subtitle !== false) {
						$subtitleString = '' . $subtitle . '<br/>';
					} else {
						$subtitleString = '';
					}
?>
				<div class="span4">
					<div class="well media">
						<a class="pull-left" href="<?=$itms_url?>"><img class="img-rounded" <?=$img_src?> height="72" width="72"/></a>
						<div class="media-body">
							<p><strong><?=$app->get_title()?></strong><br/>
								<?=$subtitleString?>
								<?=$versionString?>
							</p>
							<p>
								<a class="btn btn-primary" href="<?=$itms_url?>"><i class="icon-download icon-white"></i> Install</a>
							</p>
						</div>
					</div>
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
		<script src="/3p/holder.js"></script>
	</body>
</html>