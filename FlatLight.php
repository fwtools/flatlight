<?php

namespace FlatLight;

use \Arya\Request as Request;
use \Arya\Response as Response;

class FlatLight {
	public function main(Request $request, Response $response) {
		$css = file_get_contents(__DIR__ . '/style.css');

		foreach(glob(__DIR__ . '/modules/*.css') as $filename) {
			$css.= file_get_contents($filename);
		}

		$body = $response->getBody();
		$response->setBody($body . $css);
	}

	public function image(Request $request, Response $response, $name, $extension) {
		$response->setHeader('Content-Type', 'image/' . $extension);
		$file = __DIR__ . '/i/' . $name . '.' . $extension;

		if (file_exists($file)) {
			$response->setBody(file_get_contents($file));
		} else {
			return ['status' => 404];
		}
	}
}
