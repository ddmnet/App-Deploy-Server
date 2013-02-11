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
	var $metadata;

	function __construct($name) {
		$this->name = $name;
		$this->dir = $this->root . '/' . $this->name;
		$protocol = (isset($_SERVER['HTTPS'])) ? 'https://' : 'http://';
		$this->url = $protocol . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']);

		$this->metadata = array();

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
			} else if (preg_match('/^deployment.json$/i', $entry)) {
				$ret['deployment'] = $this->dir . '/' . $entry;
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

	function replace_variables($source, $replace) {
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
		if (empty($this->metadata)) {
			require_once('3p/plist.php');
			$plist = new Skyzyx\Components\Plist;
			$values = $plist->parseFile($_SERVER['DOCUMENT_ROOT'] . '/' . $this->contents['plist']);
			$this->metadata = $values['items'][0]['metadata'];
		}
		return (empty($this->metadata[$name]) ? false : $this->metadata[$name]);
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

	function get_plist_contents($version, $override_ipa_url = false) {
		
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
			'IPA_URL' => ($override_ipa_url === false ? $this->url . $ipa_file : $override_ipa_url),
			'ICON_72_URL' => $this->url . $this->contents['icon-72'],
			'VERSION_STR' => $version
		);
		$plist_contents = file_get_contents($this->contents['plist']);
		return $this->replace_variables($plist_contents, $replace);
	}

	function get_deployment_info() {
		if( !isset( $this->contents['deployment'] ) ) {
			return false;
		}
		return json_decode(file_get_contents( $this->contents['deployment'] ));
	}

	function deploy_via_http($version, $ipa_file, $deployment_url) {
		$post_data = array();
		$post_data['formAction'] = "create";
		$post_data['bundleName'] = $this->name;
 		$post_data['version'] = $version;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $deployment_url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		$deployment_info = json_decode(curl_exec($ch));
		curl_close($ch);

		//echo "DEPLOYMENT URL: ". $deployment_info->url . "<br>";

		$tmp_plist_filename = $this->dir . "/tmp_upload.plist";
		$tmp_ipa_filename = $this->dir . "/tmp_upload.ipa";

		$plist_contents = $this->get_plist_contents($version, $deployment_info->url);
		
		file_put_contents( $tmp_plist_filename, $plist_contents );
		copy( $ipa_file, $tmp_ipa_filename );

		$post_data = array();
		$post_data['formAction'] = "upload";
		$post_data['bundleName'] = $this->name;
		$post_data['plist_file'] = '@' . $tmp_plist_filename;
		$post_data['ipa_file'] = '@' . $tmp_ipa_filename;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $deployment_url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);


		$r = curl_exec($ch);

		$success = !($r === false);
		
		curl_close($ch);


		unlink( $tmp_plist_filename );
		unlink( $tmp_ipa_filename );
	}

	function deploy_via_ftp($version, $ipa_file, $ftp_server, $ftp_username, $ftp_password, $base_path) {
		// note: FTP transfer does not put the plist file
		$filename = $this->get_metadata('bundle-identifier');
		$destination_file = $base_path . "/" . $filename . "-" . $version . ".ipa";

		$conn_id = ftp_connect($ftp_server); 
		$login_result = ftp_login($conn_id, $ftp_username, $ftp_password); 

		// check connection
		if ((!$conn_id) || (!$login_result)) { 
		    echo "FTP connection has failed!";
		    echo "Attempted to connect to $ftp_server for user $ftp_username"; 
			return false;
		} 

		ftp_pasv($conn_id, true);

		$upload = ftp_put($conn_id, $destination_file, $ipa_file, FTP_BINARY); 
		if (!$upload) { 
			ftp_close($conn_id); 
			return false;
		}
		
		ftp_close($conn_id); 
		return true;
	}


	function deploy($version) {
		// this sends it off to our production or staging server
		// Does the following:
		// 1. Contacts the deployment server and asks for the location where the IPA will be available
		// 2. Creates plist and ipa files in temporary location
		// 3. Uploads plist file
		// 4. Uploads ipa file
		// 5. removes temporary files
		$success = false;

		// get the correct IPA for the specified version:
		$versions = $this->get_versions();
		$version_idx = array_search( $version, $versions );
		if( $version_idx === false ) {
			// if we can't find the version use the first version we find
			$ipa_file = $this->contents['ipas'][0];
			$version = $versions[0];
		} else {
			$ipa_file = $this->contents['ipas'][$version_idx];
		}

		$dinfo = $this->get_deployment_info();

		if( $dinfo->type == 'http' ) {
			$url = $dinfo->URL;
			if($url === false) {
				echo "Missing \"URL\" in deployment.json file";
				return false;
			}

			return $this->deploy_via_http($version, $ipa_file, $url);
		} else if( $dinfo->type == 'ftp' ) {

			return $this->deploy_via_ftp($version, $ipa_file, $dinfo->server, $dinfo->username, $dinfo->password, $dinfo->directory);
		}
	}

}