<?php

if(true) { // strpos($_SERVER['HTTP_REFERER'], 'welt1.freewar.de') !== FALSE
		// or strpos($_SERVER['HTTP_REFERER'], 'welt1.intercyloon.de') !== FALSE) {
	print "\n\n/* using new version */\n\n";
	include __DIR__.'/wz.css';
} else {
	print "\n\n/* using compat version */\n\n";
	include __DIR__.'/wz.compat.css';
}
