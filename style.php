<?php

error_reporting(0);
header("Content-type: text/css; charset=utf-8");

/* INCLUDES */
require_once('../../../config.php');
require_once('../../../function.php');
/* // INCLUDES */

if(isset($_COOKIE["styles"]))
	$cookie = @unserialize($_COOKIE["styles"]);
else $cookie = array();

if($cookie === false)
	$cookie = array();
$link = substr(getString('check'), 0, 5).getString('id').substr(getString('check'), -5, 5);
if(!in_array($link, $cookie))
	$cookie[] = $link;
setcookie("styles", serialize($cookie), 0, '/style');

/* DATABASE */
$db = "mysql:host={$config['db']['hostname']};";
$db .= "dbname={$config['db']['database']};charset=utf8";
$db = new PDO($db, $config['db']['username'], $config['db']['password']);
/* // DATABASE */

/* STYLEINFO */
$q = $db->prepare('SELECT * FROM fw_style WHERE id = ? AND hash = ?');
$q->execute([base_convert(getString('id'), 36, 10), getString('check')]);

if($q->rowCount() == 0)
	die('/* NO VALID STYLE-URL */');

$styleinfo = $q->fetchAll(PDO::FETCH_OBJ)[0];

$q = $db->prepare('UPDATE fw_style SET useCount = useCount + 1 WHERE id = ?');
$q->execute([base_convert(getString('id'), 36, 10)]);
/* // STYLEINFO */

/* CACHE */
$time = 120;
$exp_gmt = gmdate("D, d M Y H:i:s", time() + $time * 60) ." GMT";
$mod_gmt = gmdate("D, d M Y H:i:s", filemtime('style.css')) ." GMT";

header("Expires: " . $exp_gmt);
header("Last-Modified: " . $mod_gmt);
header("Cache-Control: private, max-age=" . ($time * 60));
header("Cache-Control: pre-check=" . $time * 60, FALSE);
/* // CACHE */

ob_start();

/* MAIN STYLE */
include 'style.min.css';
/* // MAIN STYLE */

/* ADDONS */
$settings = unserialize($styleinfo->settings);

foreach($settings->addons as $addon) {
	$addon = strtolower($addon);
	include("addon/$addon.php");
}

include 'updates.php';
AddonUpdate::run();
/* // ADDONS */

$q = $db->prepare('SELECT * FROM fw_style_usercss WHERE id = ?');
$q->execute([base_convert(getString('id'), 36, 10)]);
$data = $q->fetchAll(PDO::FETCH_OBJ);

foreach($data as $row) {
	echo $row->css;
}

echo '.framemainbg:before { content: "LightNoise v2.0"; color: #333; }';

ob_end_flush();
