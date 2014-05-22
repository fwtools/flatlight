<?php

error_reporting(0);
header("Content-type: text/css; charset=utf-8");

require __DIR__ . "/functions.php";

$addons_available = ['agg', 'at', 'msf', 'ppf'];
$addons_enabled = [];

foreach($_GET as $k => $v) {
	if(in_array($k, $addons_available)) {
		$addons_enabled[] = $k;
	}
}

sort($addons_enabled);
$name = "style-".implode("-", $addons_enabled);

/* CACHE */
$time = 240;
$exp_gmt = gmdate("D, d M Y H:i:s", time() + $time * 60) ." GMT";
$mod_gmt = gmdate("D, d M Y H:i:s", file_exists(__DIR__ . "/static/{$name}.css")
		? filemtime(__DIR__ . "/static/{$name}.css")
		: time()
) ." GMT";

header("Expires: " . $exp_gmt);
header("Last-Modified: " . $mod_gmt);
header("Cache-Control: private, max-age=" . ($time * 60));
header("Cache-Control: pre-check=" . ($time * 60), FALSE);
/* // CACHE */

if(isset($_GET['mat']) && is_string($_GET['mat'])) {
	$track_id = md5($_GET['mat']);
	print "@import 'track/track.php?{$track_id}';";
}

$world = isset($_GET['world']) && is_string($_GET['world']) ? $_GET['world'] : '';

if(!in_array($world, ['de1', 'de2', 'de3', 'de4', 'de5', 'de6', 'de7', 'de8', 'de9', 'de10', 'de11', 'de12', 'de13', 'de14'])) {
	$world = "";
}

print "@import url('event/style.php?world={$world}');";

require_once __DIR__ . "/../../../config.php";
$world_by_ref = getWorldByReferer();

if($world_by_ref != $world) {
	$db = "mysql:host={$config['db']['hostname']};";
	$db.= "dbname={$config['db']['database']};charset=utf8";
	$db = new PDO($db, $config['db']['username'], $config['db']['password']);

	$q = $db->prepare('INSERT INTO fw_flatlight_event (world, event, time) VALUES (?, ?, ?)');
	$q->execute([$world, 'ine '.$world_by_ref, time()]);

	$head = "";

	foreach (getallheaders() as $k => $v) {
	    $head.= "{$k}: {$v}\n";
	}

	$q = $db->prepare('INSERT INTO fw_flatlight_requests (head, time) VALUES (?, ?)');
	$q->execute([$head, time()]);
}

if(file_exists(__DIR__ . "/static/{$name}.css") && !isset($_GET['nocache'])) {
	print file_get_contents(__DIR__ . "/static/{$name}.css");

	if(!empty($world)) {
		require __DIR__ . "/event/pensal_addon.php";
	}

	if(!isset($_GET['world'])) {
		$currLink = "http://fw.jshack.org".$_SERVER['REQUEST_URI'];

		$newLink = strpos($currLink, '?') === false
				? "{$currLink}?world=XXX"
				: "{$currLink}&world=XXX";
		$worlds = "de1,de2,de3,...,de13,de14";

		print ".framemainbg:after { display: block; padding: 8px; margin: 8px -10px; background: #fb9; border: 1px solid rgba(0,0,0,.2); border-left: 0; border-right: 0;
			content: 'Bitte ergänze deinen Link!\AMomentan: {$currLink}\AÄnderung: {$newLink}\AXXX ist dabei deine Welt: {$worlds}'; white-space: pre; }";
	}

	exit;
}

require_once __DIR__ . "/lib/cssmin-v3.0.1-minified.php";

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

if(isset($_GET['nocache']) && !isset($_GET['skip-min'])) {
	$css_min = CssMin::minify($css, $filters, $plugins);

	file_put_contents(__DIR__ . "/static/{$name}.css", $css_min);
	print $css_min;
} else {
	print $css;
}

if(!empty($world)) {
	require __DIR__ . "/event/pensal_addon.php";
}

if(!isset($_GET['world'])) {
	$currLink = "http://fw.jshack.org".$_SERVER['REQUEST_URI'];

	$newLink = strpos($currLink, '?') === false
			? "{$currLink}?world=XXX"
			: "{$currLink}&world=XXX";
	$worlds = "de1,de2,de3,...,de13,de14";

	print ".framemainbg:after { display: block; padding: 8px; margin: 8px -10px; background: #fb9; border: 1px solid rgba(0,0,0,.2); border-left: 0; border-right: 0;
		content: 'Bitte ergänze deinen Link!\AMomentan: {$currLink}\AÄnderung: {$newLink}\AXXX ist dabei deine Welt: {$worlds}'; white-space: pre; }";
}
