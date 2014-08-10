<?php

namespace FlatLight;

use \Arya\Request as Request;

class FlatLight {
	public function main(Request $request) {
		$css = file_get_contents(__DIR__ . '/style.css');

		foreach(glob(__DIR__ . '/modules/*.css') as $filename) {
			$css.= file_get_contents($filename);
		}

		return $css;
	}
}
