<?php

$addon_helper = function($name, $display) use ($config, $db) {
	$query = $db->prepare("SELECT n.name, pn.x, pn.y FROM fw_npc AS n, fw_place_npc AS pn WHERE n.name=? && n.name = pn.npc ORDER BY n.name");
	$query->execute([$name]);
	$data = $query->fetchAll(PDO::FETCH_OBJ);

	$css = "";

	$orte = array();
	foreach($data as $row) {
		if(isset($orte[$row->x][$row->y])) {
			$orte[$row->x][$row->y] .= ' & ' . $display;
		} else {
			$orte[$row->x][$row->y] = $display;
		}
	}

	foreach($orte as $x => $arr) {
		foreach($arr as $y => $text) {
			$css .= '#mapx' . $x . 'y' . $y . ' a:after { content: "';
			$css .= str_replace(array('Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß'), array('\\0000C4', '\\0000E4', '\\0000D6', '\\0000F6', '\\0000DC', '\\0000FC', '\\0000DF'), $text);
			$css .= '"; opacity: 1; }' . "\n";
		}
	}

	return $css;
};

$addon = function() use ($addon_helper, $config, $db) {
	$css = $addon_helper('Onlo-Skelett',      'Onlo');
	$css.= $addon_helper('Ektofron',          'Ektofron');
	$css.= $addon_helper('Blattalisk',        'Blattalisk');
	$css.= $addon_helper('Untoter Bürger',    'Bürger');
	$css.= $addon_helper('temporaler Falter', 'Falter');

	$css.= '.frameitembg select[name="z_pos_id"] option[value="290"] { font-weight: bold; }';

	return $css;
};
