<?php

require_once __DIR__ . '/../../../../config.php';

$db = "mysql:host={$config['db']['hostname']};";
$db.= "dbname={$config['db']['database']};charset=utf8";
$db = new PDO($db, $config['db']['username'], $config['db']['password']);

if(!empty($_SERVER['QUERY_STRING'])) {
	$id = md5(md5(md5($_SERVER['QUERY_STRING'])));

	$query = $db->query("SELECT x,y,secure FROM fw_place WHERE x > 0 && y > 0 && x < 150 && y < 130");
	$data = $query->fetchAll(PDO::FETCH_OBJ);

	$secure = [];
	foreach($data as $row) {
		$secure[$row->x][$row->y] = $row->secure;
	}

	$place = [];

	$query = $db->prepare("SELECT * FROM fw_flatlight_track WHERE id = ?");
	$query->execute([$id]);

	$max = 1;

	if($query->rowCount()) {
		$data = $query->fetchAll(PDO::FETCH_OBJ);

		foreach($data as $row) {
			if(isset($place[$row->x][$row->y])) {
				$place[$row->x][$row->y] += 1;
			} else {
				$place[$row->x][$row->y] = 1;
			}

			if(0 == $secure[$row->x][$row->y] && $place[$row->x][$row->y] > $max) {
				$max = $place[$row->x][$row->y];
			}
		}
	}

	$query = $db->query("SELECT min(x) AS min_x, min(y) AS min_y, max(x) AS max_x, max(y) AS max_y ".
			"FROM fw_place WHERE x > 0 && y > 0 && x < 150 && y < 130");
	$data = $query->fetchAll(PDO::FETCH_OBJ);

	$min_x = $data[0]->min_x;
	$min_y = $data[0]->min_y;
	$max_x = $data[0]->max_x;
	$max_y = $data[0]->max_y;

	$map = imagecreatefrompng('map.png');

	for($x = $min_x - 1; $x <= $max_x + 1; $x++) {
		for($y = $min_y - 1; $y <= $max_y + 1; $y++) {
			if(isset($secure[$x][$y])) {
				if(!isset($place[$x][$y]))
					$place[$x][$y] = 0;

				$white = imagecolorallocatealpha($map, 0, 0, 0, (int) min((127 * (($place[$x][$y]/$max) * .9 + .1)), 127));
				imagefilledrectangle($map, ($x-$min_x+2) * 10, ($y-$min_y+2) * 10, ($x-$min_x+3) * 10 - 1, ($y-$min_y+3) * 10 - 1, $white);
			}
		}
	}

	@header('Content-Type: image/png');
	imagepng($map);
}
