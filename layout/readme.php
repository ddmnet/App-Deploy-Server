<?php

$install_url = $bundle->url . $bundle->name . '.plist';
$itms_url = "itms-services://?action=download-manifest&url=$install_url";
$icon = $bundle->get_icon_url(false);
if (file_exists($icon)) {
	$img_src = 'src="' . $icon . '"';
} else {
	$img_src = 'data-src="holder.js/72x72/social"';
}

$version = $bundle->get_metadata('bundle-version');
$versionString = '<small>v. ' . $version . '</small>';

$sizeString = '<small>' . $app->get_size() . '</small>';

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
				<p><strong><?=$bundle->get_title()?></strong> <?=$sizeString?><br/>
					<?=$subtitleString?>
					<?=$versionString?> 
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
	</div>
</div>