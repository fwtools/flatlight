<?php

$q = $db->query("SELECT * FROM fw_place WHERE secure = 1");
$data = $q->fetchAll(PDO::FETCH_OBJ);
$selectors = [];

foreach($data as $row) {
	$selectors[] = '.framemapbg #mapx' . $row->x . 'y' . $row->y . ':before';
}

echo implode(',', $selectors) . '{position:absolute;left:0;right:0;top:0;bottom:0;opacity:.8;content:url(../i/secure.png)}';
