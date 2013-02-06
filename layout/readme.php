<?php

$install_url = $bundle->url . $bundle->name . '.plist';
$itms_url = "itms-services://?action=download-manifest&url=$install_url";
$icon = $bundle->get_icon_url(false);
if (file_exists($icon)) {
	$img_src = 'src="' . $icon . '"';
} else {
	$img_src = 'data-src="holder.js/72x72/social"';
}

$published_version = $bundle->get_published_version();
$versionString = '<small>v. ' . $published_version . '</small>';

$sizeString = '<small>' . $bundle->get_size() . '</small>';

$subtitle = $bundle->get_metadata('subtitle');
if ($subtitle !== false) {
	$subtitleString = '' . $subtitle . '<br/>';
} else {
	$subtitleString = '';
}
$versions = $bundle->get_versions();
$deployment_info = $bundle->get_deployment_info();
$deployment_url = isset($deployment_info->URL) ? $deployment_info->URL : $deployment_info->server;
$can_deploy = !($deployment_info === false);

?>
<div class="row">
	<div class="span3">
		<div class="well media">
			<a class="pull-left" href="<?=$itms_url?>"><img class="img-rounded" <?=$img_src?> height="72" width="72"/></a>
			<div class="media-body">
				<p><strong><?=$bundle->get_title()?></strong><br/>
					<?=$subtitleString?>
					<?=$versionString?>, <?=$sizeString?>
				</p>
				<p>
					<a class="btn btn-primary" href="<?=$itms_url?>"><i class="icon-download icon-white"></i> Install</a>
				</p>
			</div>
		</div>
	</div>
	<div class="span9">
	<?
	echo Markdown($readme_text);
	?>

	<table class="table table-condensed">
		<tr><th>Version<th style=''>Publish<th>Install
	<?
		if( $can_deploy ) {
			echo "<th>Deploy to Production";
		}
		foreach( $versions as $ver ) {
			$active_version = strcmp( $ver, $published_version ) == 0;
			$install_url = $bundle->url . $bundle->name . '_' . $ver . '.plist';
			$publish_url = $bundle->url . $bundle->name . '_' . $ver . '.set';
			$push_url = $bundle->url . $bundle->name . '_' . $ver . '.deploy';
			$itms_url = "itms-services://?action=download-manifest&url=$install_url";
			echo "<tr><td>$ver<td>".($active_version ? "<i class='icon-ok'></i> Active" : "<a class='btn btn-small' href='$publish_url'><i class='icon-tag'></i> Publish</a>");
			echo "<td><a class='btn btn-small' href='$itms_url'><i class='icon-download'></i> Install $ver</a>";
			if( $can_deploy ) {
				echo "<td><a class='btn btn-small' href='$push_url'><i class='icon-arrow-up'></i> Deploy $ver</a>";
			}
		}

		
	?>
	
	</table>
	<? if( $can_deploy ) { echo "<i>Deploys to $deployment_url using method " . $deployment_info->type . "<br>"; } ?>
	
	</div>

</div>