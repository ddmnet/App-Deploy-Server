<?
// app list layout

$chunks = array_chunk($apps, 3);
foreach ($chunks as $row) :
?>
	<div class="row">
<?
		foreach ($row as $app):
			$contents = $app->get_contents();
			$install_url = $app->url . $app->name . '-' . $app->get_published_version() . '.plist';
			$options_url = $app->url . $app->name . '.html';
			$itms_url = "itms-services://?action=download-manifest&url=$install_url";
			$icon = $app->get_icon_url(false);
			if (file_exists($icon)) {
				$img_src = 'src="' . $icon . '"';
			} else {
				$img_src = 'data-src="holder.js/72x72/social"';
			}

			$version = $app->get_published_version();
			$versionString = '<small>v. ' . $version . '</small>';

			$subtitle = $app->get_metadata('subtitle');
			if ($subtitle !== false) {
				$subtitleString = '<br/>' . $subtitle;
			} else {
				$subtitleString = '';
			}

			$sizeString = '<small>(' . $app->get_size() . ')</small>';

			$extra_info_button = (isset($contents['readme'])) ? "<a class='btn btn-small' role='button' data-toggle='modal' data-target='#infoModal' href='" . $app->name . ".mod'><i class='icon-info-sign'></i> Info</a>" : '';
			$options_button = "<a class='btn btn-small' href='$options_url'><i class='icon-cog'></i> Options</a>";
?>
		<div class="span4">
			<div class="well media">
				<a class="pull-left" href="<?=$itms_url?>"><img class="img-rounded" <?=$img_src?> height="72" width="72"/></a>
				<div class="media-body">
					<p><strong><?=$app->get_title()?></strong> <?=$versionString?> <?=$sizeString?>
						<?=$subtitleString?>
						
					</p>
					<p>
						<a class="btn btn-primary" href="<?=$itms_url?>"><i class="icon-download icon-white"></i> Install</a>
						<?=$extra_info_button?>
						<?=$options_button?>
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
<div id="infoModal" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>App Info</h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary install"><i class="icon-download icon-white"></i> Install</a>
	</div>
</div>