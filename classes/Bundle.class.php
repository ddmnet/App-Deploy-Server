<?php

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
					} else if (preg_match('/^readme.md$/i', $entry)) {
						$ret['readme'] = $this->dir . '/' . $entry;
					} else if (preg_match('/^(icon)-?([0-9]{2,3})?\.png$/i', $entry, $matches)) {
						$icon = 'icon';
						if (!empty($matches[2])) {
							$icon .= '-' . $matches[2];
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

	function get_readme() {
		$contents = $this->get_contents();
		return (isset($contents['readme'])) ? $contents['readme'] : false;
	}

	function get_icon_url($full = true) {
		$contents = $this->get_contents();

		$ret = ($full) ? $this->url . $contents['icon-72'] : $contents['icon-72'];
		return $ret;
	}

	function get_metadata($name) {
		$ret = false;
		$contents = $this->get_contents();
		$plist = simplexml_load_file($contents['plist']);
		// find metadata dict
		$items = $plist->dict->array->dict->dict;
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

	function get_title() {
		return $this->get_metadata('title');
	}

	function get_plist_contents() {
		$contents = $this->get_contents();
		$plist_contents = file_get_contents($contents['plist']);
		return $this->replace_variables($plist_contents);
	}

}