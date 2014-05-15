<?php

$addon = function() use ($config, $db) {
	$query = $db->prepare("SELECT n.attack, pn.x, pn.y FROM fw_npc AS n, fw_place_npc AS pn WHERE n.aggressive = 1 && n.name = pn.npc && pn.x < 0 && pn.y < 0 ORDER BY n.attack");
	$query->execute();
	$data = $query->fetchAll(PDO::FETCH_OBJ);

	$css = "";
	$orte = array();
	$content = array();

	foreach($data as $row) {
		if(isset($orte[$row->x][$row->y])) {
			$orte[$row->x][$row->y] .= ', ' . str_replace('.', '', $row->attack);
		} else {
			$orte[$row->x][$row->y] = str_replace('.', '', $row->attack);
		}
	}

	foreach($orte as $x => $arr) {
		foreach($arr as $y => $text) {
			$text = str_replace('oder', ',', $text);
			$vals = explode(',', $text);
			$min = -1;
			$max = -1;

			foreach($vals as $val) {
				$val = (int) trim($val);

				if($val === 0)
					continue;

				if($min === -1 || $val < $min) {
					$min = $val;
				}

				if($max === -1 || $val > $max) {
					$max = $val;
				}
			}

			if($min === $max) {
				$text = $min;
			} else {
				$text = "$min-$max";
			}

			$content[$text][] = '#mapx' . $x . 'y' . $y . ' a:after';
		}
	}

	foreach($content as $text => $selects) {
		$css .= implode(',', $selects).'{content:"'.$text.'"}';
	}

	$css .= '.framemapbg td[id^="mapx"]{color:red;opacity:1}';
	return $css;
};
