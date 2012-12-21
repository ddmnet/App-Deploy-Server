<?

// Apps server.
// Displays a listing of apps within the /bundles directory.

define('BUNDLE_ROOT', 'bundles');

require_once 'classes/Bundle.class.php';

if (!isset($_GET['url'])) {
	$apps = array();
	// Display all the bundles.
	if ($bundlesDir = opendir(BUNDLE_ROOT)) {
		while (false !== ($entry = readdir($bundlesDir))) {
			$path = BUNDLE_ROOT . '/' . $entry;
			if (is_dir($path) && strpos($entry, '.') !== 0) {
				$bundle = new Bundle($entry);
				$contents = $bundle->get_contents();
				$is_bundle = (!empty($contents));
				if ($is_bundle) {
					$apps[] = $bundle;
				}
			}
		}
	}
	$app_list = ob_get_clean();
	$use_layout = "app_list";
	include 'layout/index.php';
} else {
	// Serve up the processed plist file.
	$uri = $_GET['url'];
	$s = explode('.', $uri);
	$bundlename = $s[0];
	$bundle = new Bundle($bundlename);
	$contents = $bundle->get_contents();
	$is_bundle = (!empty($contents));

	if ($is_bundle) {
		$type = (isset($s[1])) ? $s[1] : 'html';
		if ($type == 'plist') {
			header('Content-Type: text/xml');
			echo $bundle->get_plist_contents();
		} else {
			$readme_file = $bundle->get_readme();
			include_once '3p/markdown.php';
			if ($readme_file !== false) {
				header('Content-Type: text/html');
				$readme_text = file_get_contents($readme_file);
				if ($type == 'mod') {
					include 'layout/modal_readme.php';
				} else {
					$use_layout = "readme";
					include 'layout/index.php';
				}
			}
		}
	}
}