<?

// Apps server.
// Displays a listing of apps within the /bundles directory.

define('BUNDLE_ROOT', 'bundles');

require_once 'classes/Bundle.class.php';

if (!isset($_GET['url'])) :
	ob_start();
	// Display all the bundles.
	if ($bundlesDir = opendir(BUNDLE_ROOT)) {
		while (false !== ($entry = readdir($bundlesDir))) {
			$path = BUNDLE_ROOT . '/' . $entry;
			if (is_dir($path) && strpos($entry, '.') !== 0) {
				$bundle = new Bundle($entry);
				$contents = $bundle->get_contents();
				$is_bundle = (!empty($contents));
				if ($is_bundle) {
					$app_name = $bundle->get_title();
					$icon = $bundle->get_icon_url();
					$url = $bundle->url . $entry . '.plist';
					printf('<li><img src="%s" height="72px" width="72px;"/><a href="itms-services://?action=download-manifest&url=%s">%s</a></li>', $icon, $url, $app_name);
				}
			}
		}
	}
	$app_list = ob_get_clean();
	include 'layout/index.php';
else:
	// Serve up the processed plist file.
	header('Content-Type: text/xml');
	$uri = $_GET['url'];
	$s = explode('.', $uri);
	$bundlename = $s[0];
	$bundle = new Bundle($bundlename);
	$contents = $bundle->get_contents();
	$is_bundle = (!empty($contents));
	if ($is_bundle) {
		echo $bundle->get_plist_contents();
	}
endif;