<?php

namespace FlatLight;

use \Arya\Request as Request;
use \Arya\Response as Response;

class FlatLight {
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

	public function image(Request $request, Response $response, $name, $extension) {
		$response->setHeader('Content-Type', 'image/' . $extension);
		$file = __DIR__ . '/i/' . $name . '.' . $extension;

		if (file_exists($file)) {
			$response->setBody(file_get_contents($file));
		} else {
			return ['status' => 404];
		}
	}

	public function event(Request $request) {
		$response = new Response;

		$q = $this->db->prepare("SELECT time FROM style_event WHERE event = 'pensal-available' && world = ?");
		$q->execute([$world]);

		if($row = $q->fetch(PDO::FETCH_OBJ)) {
			$eventMin = (int) date('i', $row->time);
			$currMin = (int) date('i');

			$eventDiff = $eventMin - $eventMin % 30;
			$currDiff = $currMin - $currMin % 30;

			if($row->time > time() - 30 * 60 && $eventDiff == $currDiff) {
				$response->setHeader('Content-Type', 'text/css; charset=utf-8');

				$exp_gmt = gmdate("D, d M Y H:i:s", time() + 60 - time() % 60) ." GMT";
				$mod_gmt = gmdate("D, d M Y H:i:s", time()) ." GMT";

				$response->setHeader('Expires', $exp_gmt);
				$response->setHeader('Last-Modified', $mod_gmt);
				$response->setHeader('Cache-Control', 'private, max-age=' . (60 - time() % 60));
				$response->addHeader('Cache-Control', 'post-check=' . (60 - time() % 60));

				$response->setBody('.positiontext:after{content:"\f134"}');
				return $response;
			}
		}

		return '';
	}
}
