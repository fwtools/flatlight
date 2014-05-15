<?php

require_once __DIR__ . '/../../../../config.php';
header('Content-Type: text/css; charset=utf-8');

/* CACHE */
$time = 240;
$exp_gmt = gmdate("D, d M Y H:i:s", time() + $time * 60) ." GMT";
$mod_gmt = gmdate("D, d M Y H:i:s", time() + $time * 60) ." GMT";

header("Expires: " . $exp_gmt);
header("Last-Modified: " . $mod_gmt);
header("Cache-Control: private, max-age=" . ($time * 60));
header("Cache-Control: pre-check=" . $time * 60, FALSE);
/* /CACHE */

$hash = md5($_SERVER['QUERY_STRING']);

if(file_exists(__DIR__ . "/static/{$hash}.css")) {
	print file_get_contents(__DIR__ . "/static/{$hash}.css");
	exit;
}

$db = "mysql:host={$config['db']['hostname']};";
$db.= "dbname={$config['db']['database']};charset=utf8";
$db = new PDO($db, $config['db']['username'], $config['db']['password']);

$query = $db->prepare("SELECT * FROM fw_place WHERE x > 0 && y > 0");
$query->execute();
$data = $query->fetchAll(PDO::FETCH_OBJ);
$css = "";

foreach($data as $row) {
	$css.= "#mainmapx{$row->x}y{$row->y}{background:url('rec.php?id={$hash}&x={$row->x}&y={$row->y}')}";
}

$css.= ".imageborder{background-color: #fafafa !important}";

file_put_contents(__DIR__ . "/static/{$hash}.css", $css);
print $css;
