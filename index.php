<?

// Apps server.
// Displays a listing of apps within the /bundles directory.

define('BUNDLE_ROOT', 'bundles');

class Bundle {

	var $root = BUNDLE_ROOT;
	var $dir;
	var $name;

	function __construct($name) {
		$this->name = $name;
		$this->dir = $this->root . '/' . $this->name;
		$protocol = (isset($_SERVER['HTTPS'])) ? 'https://' : 'http://';
		$this->url = $protocol . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']);
	}

	function get_contents() {
		if (!isset($this->contents)) {
			$ret = array();
			if ($directory = opendir($this->dir)) {
				while (false !== ($entry = readdir($directory))) {
					if (preg_match('/^(.*)\.plist$/', $entry)) {
						$ret['plist'] = $this->dir . '/' . $entry;
					} else if (preg_match('/^(.*)\.ipa/', $entry)) {
						$ret['ipa'] = $this->dir . '/' . $entry;
					} else if (preg_match('/^icon-?([0-9]{2,3})?\.png$/', $entry, $matches)) {
						$icon = 'icon';
						if (!empty($matches[1])) {
							$icon .= '-' . $matches[1];
						}
						$ret[$icon] = $this->dir . '/' . $entry;
					}
				}
			}
			$this->contents = $ret;
		}
		return $this->contents;
	}

	function replace_variables($source) {
		$contents = $this->get_contents();
		$replace = array(
			'IPA_URL' => $this->url . $contents['ipa'],
			'ICON_72_URL' => $this->url . $contents['icon-72']
		);
		return str_replace(array_keys($replace), array_values($replace), $source);
	}

	function get_plist_contents() {
		$contents = $this->get_contents();
		$plist_contents = file_get_contents($contents['plist']);
		return $this->replace_variables($plist_contents);
	}

}

if (!isset($_GET['url'])) :
?>
<!DOCTYPE html>
<html>
	<body>
		<ul>
<?
	if ($bundlesDir = opendir(BUNDLE_ROOT)) {
		while (false !== ($entry = readdir($bundlesDir))) {
			$path = BUNDLE_ROOT . '/' . $entry;
			if (is_dir($path) && strpos($entry, '.') !== 0) {
				$bundle = new Bundle($entry);
				$contents = $bundle->get_contents();
				$is_bundle = (!empty($contents));
				if ($is_bundle) {
					echo '<li><a href="itms-services://?action=download-manifest&url=' . $bundle->url . $entry . '.plist">NAME</a></li>';
				}
			}
		}
	}
?>
		</ul>
		<pre>
<?
	print_r($_SERVER);
?>
		</pre>
	</body>
</html>
<?
endif;