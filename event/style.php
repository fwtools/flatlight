<?php

error_reporting(0);
header("Content-type: text/css; charset=utf-8");

require __DIR__ . "/../functions.php";

/* CACHE */
$exp_gmt = gmdate("D, d M Y H:i:s", time() + 60 - time() % 60) ." GMT";
$mod_gmt = gmdate("D, d M Y H:i:s", time()) ." GMT";

header("Expires: " . $exp_gmt);
header("Last-Modified: " . $mod_gmt);
header("Cache-Control: private, max-age=" . (60 - time() % 60));
header("Cache-Control: pre-check=" . (60 - time() % 60), FALSE);
/* // CACHE */

require_once __DIR__ . "/../lib/cssmin-v3.0.1-minified.php";
require_once __DIR__ . "/../../../../config.php";

$db = "mysql:host={$config['db']['hostname']};";
$db.= "dbname={$config['db']['database']};charset=utf8";
$db = new PDO($db, $config['db']['username'], $config['db']['password']);

require __DIR__ . "/pensal.php";
$css = $event();

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
print $css_min;
