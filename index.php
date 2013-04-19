<?

// Apps server.
// Displays a listing of apps within the /bundles directory.

define('BUNDLE_ROOT', 'bundles');

require_once 'classes/Bundle.class.php';

if (!isset($_GET['url'])) {
	$selected_group = isset($_GET['group']) ? $_GET['group'] : false;
	$bundles = load_all_bundles($selected_group);
	$all_groups = $bundles['groups'];
	$apps = $bundles['apps'];
	$app_list = ob_get_clean();
	$use_layout = "app_list";
	include 'layout/index.php';
	return;
}

// Serve up the processed plist file.
$uri = $_GET['url'];

// matches "ProjectName.extension" or "ProjectName-1.2.3.4.extension":
// if( !preg_match('/^([^\-]*)\-?([\.0-9]*)?\.([a-zA-Z]*)$/', $uri_info['basename'], $matches) ) {	// || empty($matches[2])
// 	header("HTTP/1.0 404 Not Found");
// 	return;
// }
if( !preg_match('/([^_]*)_?([\.0-9]*)?\.([a-zA-Z]*)$/', $uri, $matches) ) {	// || empty($matches[2])
	header("HTTP/1.0 404 Not Found");
	return;
}

$bundlename = $matches[1];
$version = $matches[2];
$type = $matches[3];

$bundle = new Bundle($bundlename);
if(!$bundle->is_bundle()) {
	header("HTTP/1.0 404 Not Found");
	return;
}

if ($type == 'plist') {
	header('Content-Type: text/xml');
	echo $bundle->get_plist_contents(strlen($version) > 0 ? $version : null);
} else if($type == 'set' ) {
	header("Location: /".$bundlename.".html");
	$bundle->set_published_version($version);
} else if($type == 'deploy' ) {
	header('Content-Type: text/html');
	//header("Location: /".$bundlename.".html");
	$deploy_result = $bundle->deploy($version);
	$use_layout = "deploy_result";
	include 'layout/index.php';
} else if($type == 'notify' ) {
	header('Content-Type: text/html');
	$send_results_list = $bundle->notify($version);
	$use_layout = "notify_result";
	include 'layout/index.php';
} else if($type == 'mod' || $type == 'html') {
	$readme_file = $bundle->get_readme();
	include_once '3p/markdown.php';
	header('Content-Type: text/html');
	if ($readme_file !== false) {
		$readme_text = file_get_contents($readme_file);
	} else {
		$readme_text = "";		
	}
	if ($type == 'mod') {
		include 'layout/modal_readme.php';
	} else if( $type == 'html' ) {
		$use_layout = "readme";
		include 'layout/index.php';
	}
}