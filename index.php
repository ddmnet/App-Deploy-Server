<?

// Apps server.
// Displays a listing of apps within the /bundles directory.

if (!isset($_GET['url'])) {

	if ($bundlesDir = opendir('bundles')) {
		while ($false !== ($entry = readdir($bundlesDir))) {
			echo $entry;
			if (is_dir($entry)) {
				echo 'potential IPA bundle.';
			}
		}
	}

}

echo 'hi';