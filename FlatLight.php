<?php

namespace FlatLight;

use \Arya\Request as Request;
use \Arya\Response as Response;

class FlatLight {
	public function __construct(Request $request) {
		echo 'ok';
	}

	public function main(Request $request, Response $response) {
		$css = file_get_contents(__DIR__ . '/style.css');

		foreach(glob(__DIR__ . '/modules/*.css') as $filename) {
			$css.= file_get_contents($filename);
		}

		var_dump($css);
		echo '...';

		return $response->setBody($response->getBody() . $css);
	}
}
