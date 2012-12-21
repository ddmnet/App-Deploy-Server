<?php

// Ajax readme modal

$install_url = $bundle->url . $bundle->name . '.plist';
$itms_url = "itms-services://?action=download-manifest&url=$install_url";

echo Markdown($readme_text);
?>
<a class="applink" href="<?=$itms_url?>">Install</a>