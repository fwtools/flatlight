<?php

class AddonUpdate {
	static function runFreewar() {
		$html = fetchPage('http://forum.freewar.de/viewforum.php?f=8');
		$pattern = '$<a title="Verfasst: (.*)" href="(.*)" class="topictitle">(.*)</a>$';
		preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);
		
		$month_de  = ['Mär', 'Mai', 'Okt', 'Dez'];
		$month_en = ['Mar', 'May', 'Oct', 'Dec'];
		
		foreach($matches as $match) {
			$timestr = str_replace($month_de, $month_en, $match[1]);
			$time = strtotime($timestr);
			$events[$time] = $match[3];
		}

		krsort($events);
		$i = 0;
		
		$css = '.framebannerbg:';
		
		$css .= (int) date('d') % 2 == 0 ? 'before' : 'after';

		$css .= " { background: #85bd91; content: 'aktuelle Freewar-Updates \\A ";
		
		foreach($events as $time => $text) {
			if(++$i < 3) {
				$css .= date('d.m.Y', $time) . ': ' . $text . " \\A ";
			}
		}
		
		return "$css'}";
	}
	
	static function runStyle() {
		$events = [
			'31.01.2014 (2)' => 'Chatfarben für Gruppen und GC geändert.',
			'31.01.2014 (1)' => 'Wettertexte sind nun kursiv.',
			# '06.01.2014' => 'Sponsorablauf wird hervorgehoben.',
			# '29.10.2013' => 'Aggressive NPCs im Nebelsumpf gefixt.',
			# '11.10.2013' => 'Rassen von Clanmitgliedern im Clanmenü \\A und Inventar-Fix bei Intelligenz und Geld',
			# '09.10.2013' => 'Neues Bannerframedesign: Freewar- & Style-Updates',
			# '08.10.2013' => 'Verbessertes Caching, Fix:Zauberkugelindikator'
		];
		
		$css = '.framebannerbg:';
		
		$css .= (int) date('d') % 2 == 0 ? 'after' : 'before';

		$css .= ' { background: #85b9bd; content: "aktuelle Style-Updates \\A ';
		foreach($events as $time => $text) {
			$css .= $time . ': ' . $text . " \\A ";
		}
		
		return $css.'"}';
	}
	
	static function run() {
		$css = self::runFreewar();
		$css .= self::runStyle();
		
		$special_chars = ['Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß'];
		$special_esc = ['\\0000C4', '\\0000E4', '\\0000D6', '\\0000F6', 
						'\\0000DC', '\\0000FC', '\\0000DF'];
		
		echo str_replace($special_chars, $special_esc, $css);
	}
}