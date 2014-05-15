<?php

require_once __DIR__ . '/../../../../config.php';

$db = "mysql:host={$config['db']['hostname']};";
$db.= "dbname={$config['db']['database']};charset=utf8";
$db = new PDO($db, $config['db']['username'], $config['db']['password']);

/* CACHE */
$exp_gmt = gmdate("D, d M Y H:i:s", time() + 1) ." GMT";
$mod_gmt = gmdate("D, d M Y H:i:s", time()) ." GMT";

header("Expires: " . $exp_gmt);
header("Last-Modified: " . $mod_gmt);
header("Cache-Control: private, max-age=1");
header("Cache-Control: pre-check=1", FALSE);
/* //CACHE */

if(isset($_GET['x'], $_GET['y'], $_GET['id']) && is_string($_GET['x']) && is_string($_GET['y']) && is_string($_GET['id'])) {
	$x = $_GET['x'];
	$y = $_GET['y'];
	$id = md5($_GET['id']);

	$query = $db->prepare("SELECT * FROM fw_flatlight_track WHERE id = ? ORDER BY time DESC LIMIT 1");
	$query->execute([$id]);
	$data = $query->fetchAll(PDO::FETCH_OBJ);

	if(!($query->rowCount() && $x == $data[0]->x && $y == $data[0]->y)) {
		$query = $db->prepare("INSERT INTO fw_flatlight_track(id, x, y, time) VALUES(?, ?, ?, NOW())");
		$query->execute([$id, $x, $y]);
	}
}
