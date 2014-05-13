<?php

require_once(__DIR__.'/../../../../config.php');

if(!file_exists(__DIR__.'/at.css') or filemtime(__DIR__.'/at.css') < time() - 120*60) {
	function updateAuftragsNPC($name, $display) {
		global $config;
		
		$db = "mysql:host={$config['db']['hostname']};";
		$db .= "dbname={$config['db']['database']};charset=utf8";
		$db = new PDO($db, $config['db']['username'], $config['db']['password']);
		
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
	}

	$css_gesamt  = updateAuftragsNPC('Onlo-Skelett', 'Onlo');
	$css_gesamt .= updateAuftragsNPC('Ektofron', 'Ektofron');
	$css_gesamt .= updateAuftragsNPC('Blattalisk', 'Blattalisk');
	$css_gesamt .= updateAuftragsNPC('Untoter Bürger', 'Bürger');
	$css_gesamt .= updateAuftragsNPC('temporaler Falter', 'Falter');

	$css_gesamt .= "\n\n" . '/* > gepresste Zauberkugel */
	/* > > Mento AH */ 
	.frameitembg select[name="z_pos_id"] option[value="290"] {
		font-weight: bold;
	}' . "\n\n";

	file_put_contents(__DIR__.'/at.css', $css_gesamt);
}

include __DIR__.'/at.css';