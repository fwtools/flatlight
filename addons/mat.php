<?php

if(!isset($styleinfo))
	die;

$css = ob_get_contents();
ob_end_clean();

$link = substr($styleinfo->hash, 0, 5).base_convert($styleinfo->id, 10, 36).substr($styleinfo->hash, 5);
echo "@import url('../track/track.php?$link');\n";

ob_start();
echo $css;