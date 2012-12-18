<?
// app list layout

		$chunks = array_chunk($apps, 3);
		foreach ($chunks as $row) :
?>
			<div class="row">
<?
				foreach ($row as $app):
					$contents = $app->get_contents();
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
						$subtitleString = '<br/>' . $subtitle;
					} else {
						$subtitleString = '';
					}

					$sizeString = '<small>(' . $app->get_size() . ')</small>';

					$extra_info_button = (isset($contents['readme'])) ? "<a class='btn' href='" . $app->name . "'><i class='icon-info-sign'></i> Info</a>" : '';
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