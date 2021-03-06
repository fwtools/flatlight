<?php

namespace FlatLight;

use \Arya\Request as Request;
use \Arya\Response as Response;

class FlatLight {
	private $allowedImageExtensions = ['png', 'gif', 'jpg'];
	private $db;

	public function __construct(\PDO $db) {
		$this->db = $db;
	}

	public function main(Request $request, Response $response) {
		$css = file_get_contents(__DIR__ . '/style.css');

		foreach(glob(__DIR__ . '/modules/*.css') as $filename) {
			$css.= file_get_contents($filename);
		}

		$body = $response->getBody();
		$response->setBody($body . $css);
	}

	public function image(Request $request, $name, $extension) {
		if(!in_array($extension, $this->allowedImageExtensions)) {
			return ['status' => 404];
		}

		$response = new Response;
		$response->setHeader('Content-Type', 'image/' . $extension);
		$file = __DIR__ . '/i/' . $name . '.' . $extension;

		if (file_exists($file)) {
			$exp_gmt = gmdate("D, d M Y H:i:s", time() + 86400) ." GMT";
			$mod_gmt = gmdate("D, d M Y H:i:s", filemtime($file)) ." GMT";

			$response->setHeader('Expires', $exp_gmt);
			$response->setHeader('Last-Modified', $mod_gmt);
			$response->setHeader('Cache-Control', 'private, max-age=86400');
			$response->addHeader('Cache-Control', 'pre-check=86400');
			$response->setBody(file_get_contents($file));

			return $response;
		} else {
			return ['status' => 404];
		}
	}

	public function event(Request $request) {
		$response = new Response;

		$exp_gmt = gmdate("D, d M Y H:i:s", time() - time() % 1800 + 1800) ." GMT";
		$mod_gmt = gmdate("D, d M Y H:i:s", time() - time() % 1800) ." GMT";

		$response->setHeader('Expires', $exp_gmt);
		$response->setHeader('Last-Modified', $mod_gmt);
		$response->setHeader('Cache-Control', 'private, max-age='.(1800 - time() % 1800));
		$response->addHeader('Cache-Control', 'pre-check='.(1800 - time() % 1800));

		srand((integer) (time() / 1800) + 1184);
		if (rand(1, 3) == 1) {
			return $response->setBody('');
		} else {
			$response->setHeader('Content-Type', 'text/css; charset=utf-8');
			$response->setBody('.positiontext:after{content:"\f134"}');
			return $response;
		}
	}
}
