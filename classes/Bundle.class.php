<?php

function load_all_bundles() {
	// Load all the bundles.
	if( !($bundlesDir = opendir(BUNDLE_ROOT)) ) {
		return array();
	}

	$apps = array();
	while( ($entry = readdir($bundlesDir)) ) {
		$path = BUNDLE_ROOT . '/' . $entry;
		if ( !is_dir($path) || strpos($entry, '.') === 0) {
			continue;
		}

		$bundle = new Bundle($entry);
		if(!$bundle->is_bundle()) {
			continue;
		}

		$apps[] = $bundle;
	}

	return $apps;
}

class Bundle {

	var $root = BUNDLE_ROOT;
	var $dir;
	var $name;

	function __construct($name) {
		$this->name = $name;
		$this->dir = $this->root . '/' . $this->name;
		$protocol = (isset($_SERVER['HTTPS'])) ? 'https://' : 'http://';
		$this->url = $protocol . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']);

		$this->get_contents();	// it's being call everywhere anyway, might as well do it right away
	}

	function is_bundle() {
		return !empty($this->contents);
	}

	function get_contents() {
		if (isset($this->contents)) {
			return $this->contents;
		}
		$ret = array();
		$ret['ipas'] = array();
		if ( !($directory = opendir($this->dir)) )  {
			return $ret;
		}
		while (false !== ($entry = readdir($directory))) {
			if (preg_match('/^(.*)\.plist$/', $entry)) {
				$ret['plist'] = $this->dir . '/' . $entry;
			} else if (preg_match('/^(.*)\.ipa/', $entry)) {
				$ret['ipas'][] = $this->dir . '/' . $entry;
			} else if (preg_match('/^readme.md$/i', $entry)) {
				$ret['readme'] = $this->dir . '/' . $entry;
			} else if (preg_match('/^active.txt$/i', $entry)) {
				$ret['active'] = $this->dir . '/' . $entry;
			} else if (preg_match('/^(icon)-?([0-9]{2,3})?\.png$/i', $entry, $matches)) {
				$icon = 'icon';
				if (!empty($matches[2])) {
					$icon .= '-' . $matches[2];
				}
				$ret[$icon] = $this->dir . '/' . $entry;
			}
		}

		$this->contents = $ret;
		
		return $this->contents;
	}

	function get_versions() {
		$versions = array();
		foreach( $this->contents['ipas'] as $ipa_filename ) {
			if( !preg_match('/^(.*)-([\.0-9]*)\.ipa$/', $ipa_filename, $matches) || empty($matches[2]) ) {
				continue;
			}
			$versions[] = $matches[2];
		}
		if( count($this->contents['ipas']) > 0 && count($versions) == 0) {
			// maybe we have a version number in the plist file?
			$versions[] = $this->get_metadata('bundle-version');
		}
		return $versions;
	}

	function replace_variables($source, $version) {
		$versions = $this->get_versions();
		$version_idx = array_search( $version, $versions );
		if( $version_idx === false ) {
			// if we can't find the version use the first version we find
			$ipa_file = $this->contents['ipas'][0];
			$version = $versions[0];
		} else {
			$ipa_file = $this->contents['ipas'][$version_idx];
		}

		$replace = array(
			'IPA_URL' => $this->url . $ipa_file,
			'ICON_72_URL' => $this->url . $this->contents['icon-72'],
			'VERSION_STR' => $version
		);
		return str_replace(array_keys($replace), array_values($replace), $source);
	}

	function get_readme() {
		return (isset($this->contents['readme'])) ? $this->contents['readme'] : false;
	}

	function get_icon_url($full = true) {
		$ret = ($full) ? $this->url . $this->contents['icon-72'] : $this->contents['icon-72'];
		return $ret;
	}

	function get_size($human_readable = true) {
		$ret = filesize($this->contents['ipas'][0]);
		if ($human_readable) {
			$sizes = array("B","KB","MB","GB","TB","PB","EB","ZB","YB");
		    $ret = number_format($ret/pow(1024, $p = floor(log($ret, 1024))), 1) . ' ' . $sizes[$p];
		}
		return $ret;
	}

	function get_metadata($name) {
		$ret = false;
		$plist = simplexml_load_file($this->contents['plist']);
		// find metadata dict
		$items = $plist->dict->array->dict->dict;	// ewww
		$n = 0;
		$tindex = false;
		foreach ($items->key as $key) {
			if ($key == $name) {
				$tindex = $n;
				break;
			} else {
				$n++;
			}
		}
		if ($tindex) {
			$ret = $items->string[$tindex];
		}
		return $ret;
	}

	function get_published_version() {
		if( !isset($this->contents['active']) ) {
			// no active file so use first version:
			$versions = $this->get_versions();
			return $versions[0];
		}
		$active_version = file_get_contents($this->contents['active']);
		$versions = $this->get_versions();
		$version_idx = array_search( $active_version, $versions );
		if( $version_idx === false ) {

		} else {

		}
		return $versions[$version_idx];
	}

	function set_published_version( $version ) {
		file_put_contents( $this->contents['active'], $version );
	}

	function get_title() {
		return $this->get_metadata('title');
	}

	function get_plist_contents($version) {
		$plist_contents = file_get_contents($this->contents['plist']);
		return $this->replace_variables($plist_contents, $version);
	}

	function deploy_current_app() {
		

	}

}