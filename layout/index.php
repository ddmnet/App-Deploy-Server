<!DOCTYPE html>
<html>
	<head>
		<title>DDM Apps <? if( $selected_group !== false ) :?> : <?=$selected_group?> <? endif; ?></title>
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
		
					<ul class="nav">

						<? if( count($all_groups) > 0 ) : ?>
      					<li>
<div class="btn-group">
  <a class="btn btn-inverse dropdown-toggle" data-toggle="dropdown" href="#">
    Groups
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
  	<? foreach( $all_groups as $group ) : ?>
    <li <? if( $group == $selected_group ) : ?>class="active"<? endif; ?> ><a href='/?group=<?=$group?>'><?=$group?></a></li>
    <? endforeach; ?>
  </ul>
</div>
      					</li><? endif; ?>
					</ul>
				</div>
			</div>
		</div>
		<div id="primary" class="container">
<?
			include $use_layout . '.php';
?>
		</div>
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<script src="/3p/bootstrap/js/bootstrap.min.js"></script>
		<script src="/3p/holder.js"></script>
		<script type="text/javascript" src="/js/behavior.js"></script>
	</body>
</html>