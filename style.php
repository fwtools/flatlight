<?php

error_reporting(0);
header("Content-type: text/css; charset=utf-8");

/* CACHE */
$time = 240;
$exp_gmt = gmdate("D, d M Y H:i:s", time() + $time * 60) ." GMT";
$mod_gmt = gmdate("D, d M Y H:i:s", filemtime(__DIR__ . "/style.php")) ." GMT";

header("Expires: " . $exp_gmt);
header("Last-Modified: " . $mod_gmt);
header("Cache-Control: private, max-age=" . ($time * 60));
header("Cache-Control: pre-check=" . ($time * 60), FALSE);
/* // CACHE */

if(isset($_GET['mat']) && is_string($_GET['mat'])) {
	$track_id = md5($_GET['mat']);
	print "@import 'track/track.php?{$track_id}';";
}

$addons_available = ['agg', 'at', 'msf'];
$addons_enabled = [];

foreach($_GET as $k => $v) {
	if(in_array($k, $addons_available)) {
		$addons_enabled[] = $k;
	}
}

sort($addons_enabled);

$name = "style-".implode("-", $addons_enabled);
if(file_exists(__DIR__ . "/static/{$name}.css") && !isset($_GET['nocache'])) {
	print file_get_contents(__DIR__ . "/static/{$name}.css");
	exit;
}

require_once __DIR__ . "/lib/cssmin-v3.0.1-minified.php";
require_once __DIR__ . "/../../../config.php";

$db = "mysql:host={$config['db']['hostname']};";
$db.= "dbname={$config['db']['database']};charset=utf8";
$db = new PDO($db, $config['db']['username'], $config['db']['password']);

$css = file_get_contents(__DIR__ . "/style.css");

foreach(scandir(__DIR__ . "/components") as $file) {
	if($file === '.' || $file === '..') {
		continue;
	}

	$css.= file_get_contents(__DIR__ . "/components/{$file}");
}

foreach($addons_enabled as $addon) {
	if(file_exists(__DIR__ . "/addons/{$addon}.php")) {
		require __DIR__ . "/addons/{$addon}.php";
		$css.= $addon();
	}
}

$filters = [
	"ImportImports"                 => array("BasePath" => "components"),
    "RemoveComments"                => true,
    "RemoveEmptyRulesets"           => true,
    "RemoveEmptyAtBlocks"           => true,
    "ConvertLevel3AtKeyframes"      => false,
    "ConvertLevel3Properties"       => true,
    "Variables"                     => true,
    "RemoveLastDelarationSemiColon" => true
];

$plugins = [
	"Variables"                     => true,
	"ConvertFontWeight"             => true,
	"ConvertHslColors"              => true,
	"ConvertRgbColors"              => true,
	"ConvertNamedColors"            => true,
	"CompressColorValues"           => true,
	"CompressUnitValues"            => true,
	"CompressExpressionValues"      => true
];

$css_min = CssMin::minify($css, $filters, $plugins);

file_put_contents(__DIR__ . "/static/{$name}.css", $css_min);
print $css_min;
