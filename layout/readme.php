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
		<tr><th style=''>Publish<th>Version<th>Install<th>Push to Destination
	<?
		$versions = $bundle->get_versions();
		foreach( $versions as $ver ) {
			$active_version = strcmp( $ver, $published_version ) == 0;
			$install_url = $bundle->url . $bundle->name . '-' . $ver . '.plist';
			$publish_url = $bundle->url . $bundle->name . '-' . $ver . '.set';
			$push_url = $bundle->url . $bundle->name . '-' . $ver . '.push';
			$itms_url = "itms-services://?action=download-manifest&url=$install_url";
			echo "<tr><td>".($active_version ? "<i class='icon-ok'></i> Active" : "<a class='btn btn-small' href='$publish_url'><i class='icon-tag'></i> Publish</a>") . "<td>$ver";
			echo "<td><a class='btn btn-small' href='$itms_url'><i class='icon-download'></i> Install $ver</a>";
			echo "<td><a class='btn btn-small' href='$push_url'><i class='icon-arrow-up'></i> Push $ver</a>";
		}
	?>
	
	</table>
	
	</div>

</div>